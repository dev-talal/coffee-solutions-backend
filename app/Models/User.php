<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;
    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'location',
        'added_by',
        'employe_number',
        'otp',
        'otp_expires_at',
        'customer_code',
        'registration_number',
        'vat_number',
        'region_id',
        'city_id',
        'warehouse_id',
        'customer_category_id',
        'customer_care_id',
        'sales_id',
        'credit_limit',
        'profile',
        'company_name',
        'ar_company_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'public_key',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function warehouses()
    {
        return $this->belongsToMany(WareHouse::class, 'staff_warehouses', 'user_id', 'warehouse_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function customerCategory()
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_category_id');
    }

    public function customerCare()
    {
        return $this->belongsTo(User::class, 'customer_care_id');
    }

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(WareHouse::class, 'warehouse_id');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class, 'user_id');
    }

    public function deliveryAddress()
    {
        return $this->hasMany(UserDeliveryAddress::class, 'user_id');
    }

    public function getCustomerRoom()
    {
        if ($this->hasRole('customer')) {
            return $this->hasOne(Room::class, 'user_id', 'id');
        }

        return null;
    }
}
