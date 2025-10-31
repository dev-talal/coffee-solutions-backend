<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\DriverRequest;
use App\Services\DriverService;
use App\Http\Resources\DriverResource;

class DriverController extends Controller
{
    use ApiResponseTrait;
    protected $drivers;
    public function __construct(DriverService $drivers)
    {
        $this->drivers = $drivers;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $drivers = $this->drivers->paginate(10);
        return $this->successCollection($drivers, DriverResource::class, 'Drivers retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DriverRequest $request)
    {
        $driver = $request->validated();
        $driver = $this->drivers->create($driver);
        return $this->successResource($driver, DriverResource::class, 'Driver created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $driver = $this->drivers->find($id);
        if (!$driver) {
            return $this->error('Driver not found', 404);
        }
        return $this->successResource($driver, DriverResource::class, 'Driver retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DriverRequest $request, string $id)
    {
        $driver = $request->validated();
        $driver = $this->drivers->update($driver, $id);
        return $this->successResource($driver, DriverResource::class, 'Driver updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $driver = $this->drivers->find($id);
        if (!$driver) {
            return $this->error('Driver not found', 404);
        }
        $driver->warehouses()->detach();
        $this->drivers->delete($id); 
        return $this->success(null, 'Driver deleted successfully');
    }

    public function getDriversByOrder(string $orderId)
    {
        $drivers = $this->drivers->getDriversByOrder($orderId);
        return $this->successCollection($drivers, DriverResource::class, 'Drivers retrieved successfully');
    }
}
