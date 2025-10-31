<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Services\RegionService;
use App\Services\CityService;
use App\Services\UserService;
use App\Services\WareHouseService;
use App\Http\Resources\CompactRegionResource;
use App\Http\Resources\CompactCityResource;
use App\Http\Resources\CompactWareHouseResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\CustomerCategory;
use App\Repositories\Contracts\CustomerCategoryRepositoryInterface;
use App\Models\WareHouse;

class CustomerSettingController extends Controller
{
    use ApiResponseTrait;
    protected $regions, $cities, $warehouses, $userService, $customerCategories;
    public function __construct(
        RegionService $regions, CityService $cities,
         WareHouseService $warehouses, UserService $userService,
        CustomerCategoryRepositoryInterface $customerCategories
        )
    {
        $this->regions = $regions;
        $this->cities = $cities;
        $this->warehouses = $warehouses;
        $this->userService = $userService;
        $this->customerCategories = $customerCategories;
    }
    /**
     * Display a listing of the resource.
     */
    public function getRegions()
    {
        $regions = $this->regions->all();
        return $this->successCollection($regions, CompactRegionResource::class, 'Regions retrieved successfully');
    }

    /**
     * Display the cities for a specific region.
     */
    public function getCities(string $id)
    {
        $cities = $this->cities->getCitiesByRegion($id);
        $warehouses = $this->warehouses->getWarehousesByRegion($id);

        return $this->successData([
            'cities' => CompactCityResource::collection($cities),
            'warehouses' => CompactWareHouseResource::collection($warehouses),
        ], 'Data retrieved successfully');
    }

    /**
     * Display the customer care users for a specific warehouse.
     */
    public function getCustomerCareUsers(string $id)
    {
        $customerCareUsers = $this->userService->getStaffByWarehouse($id, 'customer Care');
        $salesUsers = $this->userService->getStaffByWarehouse($id, 'sales');
        return $this->successData([
            'customer_care_users' => UserResource::collection($customerCareUsers),
            'sales_users' => UserResource::collection($salesUsers),
        ], 'Customer care and sales users retrieved successfully');
    }

    /**
     * Display the customer categories.
     */
    public function getCustomerCategories()
    {
        $categories = $this->customerCategories->all();
        return $this->successCollection($categories, CustomerCategory::class, 'Customer categories retrieved successfully');
    }

    /**
     * Display the warehouses.
     */
    public function getWarehouses()
    {
        $warehouses = WareHouse::all();
        return $this->successCollection($warehouses, CompactWareHouseResource::class, 'Warehouses retrieved successfully');
    }
}
