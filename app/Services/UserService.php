<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use App\Models\UserDeliveryAddress;
use App\Notifications\SendOtpNotification;
use Carbon\Carbon;
use App\Services\CommonService;
use App\Mail\WelcomeMail;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class UserService
{
    protected UserRepositoryInterface $userRepository;
    protected CommonService $commonService;

    public function __construct(UserRepositoryInterface $userRepository, CommonService $commonService)
    {
        $this->userRepository = $userRepository;
        $this->commonService = $commonService;
    }

    public function getStaff(string $searchFor = '', array $wareHouses = [])
    {
        return $this->userRepository->getUsersByRole('customer', true , $searchFor, $wareHouses);
    }

    public function getCustomers(string $searchFor = '', array $wareHouses = [])
    {
        return $this->userRepository->getUsersByRole('customer', false, $searchFor, $wareHouses);
    }

    public function findByEmail(string $email)
    {
        return $this->userRepository->findByEmail($email);
    }

    public function update(array $data, string $id)
    {
        if (isset($data['profile'])) {
            $path = $this->commonService->uploadFile($data['profile'], 'profiles');
            if ($path) {
                $data['profile'] = $path;
            }
        } else {
            unset($data['profile']);
        }
        return $this->userRepository->update($id, $data);
    }

    public function createWithRole(array $data, string $role)
    {
        $customerTransformData = $this->transformCustomer($data);
        $customerDeliveryAddressData = $this->transformCustomerDeliveryAddress($data);
        $plainPassword = $customerTransformData['password']; 
        $customerTransformData['password'] = bcrypt($customerTransformData['password']);
        $user = $this->userRepository->create($customerTransformData);

        if ($user) {
            $user->assignRole($role);
            Mail::to($user->email)->send(
                new WelcomeMail($user->email, $plainPassword, $role === 'customer')
            );

            if(isset($customerDeliveryAddressData['is_link']) && $customerDeliveryAddressData['is_link'] != 2){
                $user->deliveryAddress()->create($customerDeliveryAddressData);
            }
        }

        if (isset($data['warehouse_ids']) && is_array($data['warehouse_ids'])) {
            $this->saveWarehouse($data, $user);
        }
        return $user;
    }

   public function updateStaff(array $data, string $id): User|false
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return false;
        }

        $customerTransformData = $this->transformCustomer($data);
        $customerDeliveryAddressData = $this->transformCustomerDeliveryAddress($data);

        if (!empty($customerTransformData['password'])) {
            $customerTransformData['password'] = bcrypt($customerTransformData['password']);
        } else {
            unset($customerTransformData['password']);
        }

        $updated = $this->userRepository->update($id, $customerTransformData);

        if (!$updated) {
            return false;
        }

        if(isset($customerDeliveryAddressData['is_link']) && $customerDeliveryAddressData['is_link'] != 2){
            if(isset($customerDeliveryAddressData['address_id']) && $customerDeliveryAddressData['address_id'] != ''){
                $deliveryId = $customerDeliveryAddressData['address_id'];
                unset($customerDeliveryAddressData['address_id']);
                UserDeliveryAddress::where('id', $deliveryId)->update($customerDeliveryAddressData);
            }else{
                $user->deliveryAddress()->create($customerDeliveryAddressData);
            }
        }

        if (isset($data['warehouse_ids']) && is_array($data['warehouse_ids'])) {
            $this->saveWarehouse($data, $user);
        }

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user->fresh(); // return the updated user
    }

    public function find(string $id): User
    {
        return $this->userRepository->find($id);
    }

    public function delete(string $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function forgetPassword(string $email)
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return false;
        }
        $user->otp = rand(100000, 999999); // Generate a random OTP
        $user->otp_expires_at = now()->addMinutes(10); // Set OTP expiration time
        $user->save();
        // Send OTP notification
        $user->notify(new SendOtpNotification($user->otp));
        return true;
    }

    public function resetPassword(string $email, int $otp, string $password)
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user || $user->otp != $otp || now()->greaterThan($user->otp_expires_at)) {
            return false;
        }
        $user->password = bcrypt($password);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();
        return true;
    }

    public function getStaffByWarehouse(string $warehouseId, string $role = '' ) 
    {
        return $this->userRepository->getStaffByWarehouse($warehouseId, $role);
    }

    public function changePassword(string $oldPassword, string $newPassword): bool
    {
        $user = auth()->user();
        if (!$user || !password_verify($oldPassword, $user->password)) {
            return false; // Old password is incorrect
        }
        $user->password = bcrypt($newPassword);
        return $user->save();
    }

    public function saveWarehouse(array $data, $user)
    {
        $user->warehouses()->detach(); // Clear existing warehouses
        foreach ($data['warehouse_ids'] as $warehouseId) {
            $user->warehouses()->attach($warehouseId);
        }
    }

    public function transformCustomer(array $data)
    {
       $data = Arr::only($data, [
           'first_name', 'last_name', 'company_name', 'ar_company_name', 'email', 'password',
           'customer_code','registration_number','vat_number','region_id', 'city_id', 'warehouse_id', 'delivery_address','phone',
           'customer_category_id', 'customer_care_id','sales_id', 'credit_limit','employe_number', 'location'
        ]);
        return $data;
    }

    public function transformCustomerDeliveryAddress(array $data)
    {
        $data = Arr::only($data, [
            'is_link', 'short_address', 'building_number', 'secondary_number', 'postal_code', 'city', 'address_link', 'ar_short_address', 'ar_building_number', 'ar_secondary_number', 'ar_postal_code', 'ar_city','address_id'
        ]);
        return $data;
    }
}
