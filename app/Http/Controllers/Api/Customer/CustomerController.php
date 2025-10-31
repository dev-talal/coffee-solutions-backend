<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\CustomerResource;
use App\Services\UserService;
use App\Services\RoomService;
use App\Http\Resources\CartResource;

class CustomerController extends Controller
{
    use ApiResponseTrait;
    protected $userService;
    protected $roomService;
    public function __construct(UserService $userService, RoomService $roomService)
    {
        $this->userService = $userService;
        $this->roomService = $roomService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $wareHouses = staffWareHouse(auth()->user());
        $searchFor = $request->query('search', '');
        $customers = $this->userService->getCustomers($searchFor, $wareHouses);
        return $this->successCollection($customers, CustomerResource::class, 'Customer retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {
        $customer = $request->validated();
        $customer['added_by'] = auth()->user()->id;
        $customer = $this->userService->createWithRole($customer, 'customer');
        if($customer) {
            $this->roomService->createRoom([
                'user_id' => $customer->id,
                'customer_care_agent' => $customer->customer_care_id
            ]);
        }
        return $this->successResource($customer, CustomerResource::class, 'Customer created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = $this->userService->find($id);
        if (!$customer) {
            return $this->error('Customer not found', 404);
        }
        return $this->successResource($customer, CustomerResource::class, 'Customer retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, string $id)
    {
        $customer = $request->validated();
        $customer = $this->userService->updateStaff($customer, $id);
        return $this->successResource($customer, CustomerResource::class, 'Customer updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = $this->userService->find($id);
        if (!$customer) {
            return $this->error('Customer not found', 404);
        }
        $this->userService->delete($id);
        return $this->success(null,'Customer deleted successfully');
    }

    public function getCustomerCart(string $id)
    {
        $customer = $this->userService->find($id);
        if (!$customer) {
            return $this->error('Customer not found', 404);
        }

        CartResource::$customer = $customer;

        return $this->successCollection(
            $customer->cart,
            CartResource::class,
            'Customer cart retrieved successfully'
        );
    }
}
