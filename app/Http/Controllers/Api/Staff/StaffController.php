<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\StaffResource;
use App\Http\Resources\CompactStaffResource;
use App\Traits\ApiResponseTrait;
use App\Services\UserService;
use App\Http\Requests\StoreStaffRequest;

class StaffController extends Controller
{
    use ApiResponseTrait;
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $wareHouses = staffWareHouse(auth()->user());
        $searchFor = $request->query('search', '');
        $staff = $this->userService->getStaff($searchFor, $wareHouses);
        return $this->successCollection($staff, StaffResource::class, 'Staff retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStaffRequest $request)
    {
        $staff = $request->validated();
        $staff['added_by'] = auth()->user()->id;
        $user = $this->userService->createWithRole($staff, $staff['role']);
        return $user
        ? $this->successResource($user, StaffResource::class, 'Staff created successfully')
        : $this->error('Failed to create staff', 500);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $staff = $this->userService->find($id);
        if (!$staff) {
            return $this->error('Staff not found', 404);
        }
        return $this->successResource($staff, StaffResource::class, 'Staff retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreStaffRequest $request, string $id)
    {
        $staffData = $request->validated();
        $staff = $this->userService->updateStaff($staffData, $id);
        return $this->successResource($staff, StaffResource::class, 'Staff updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $staff = $this->userService->find($id);
        if (!$staff) {
            return $this->error('Staff not found', 404);
        }
        $this->userService->delete($id);
        return $this->success('Staff deleted successfully');
    }

    /**
     * Get staff by warehouse
     */
    public function getStaffByWarehouse(string $warehouseId)
    {
        $staff = $this->userService->getStaffByWarehouse($warehouseId);
        return $this->successCollection($staff, CompactStaffResource::class, 'Staff retrieved successfully');
    }
}
