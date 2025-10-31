<?php

namespace App\Services;
use App\Models\Driver;
use App\Models\Order;
use App\Repositories\Contracts\DriverRepositoryInterface;

class DriverService
{
    protected DriverRepositoryInterface $driverRepository;

    public function __construct(DriverRepositoryInterface $driverRepository)
    {
        $this->driverRepository = $driverRepository;
    }

    public function paginate(int $perPage = 10)
    {
        return $this->driverRepository->paginate($perPage);
    }

    public function all()
    {
        return $this->driverRepository->all();
    }

    public function find(string $id)
    {
        return $this->driverRepository->find($id);
    }

    public function create(array $data)
    {
        $warehouses = isset($data['warehouse_ids']) && is_array($data['warehouse_ids']) ? $data['warehouse_ids'] : [];
        unset($data['warehouse_ids']);
        $driver = $this->driverRepository->create($data);
        if($driver) {
            $this->saveWarehouse($warehouses, $driver);
        }

        return $driver;
    }

    public function saveWarehouse(array $warehouses, $driver)
    {
        $driver->warehouses()->detach(); 
        foreach ($warehouses as $warehouseId) {
            $driver->warehouses()->attach($warehouseId);
        }
    }

    public function update(array $data, string $id)
    {
        $warehouses = isset($data['warehouse_ids']) && is_array($data['warehouse_ids']) ? $data['warehouse_ids'] : [];
        unset($data['warehouse_ids']);
        $driver = $this->driverRepository->update($id, $data);
        if($driver) {
            $this->saveWarehouse($warehouses, $driver);
        }
        return $driver->fresh();
    }

    public function delete(string $id): bool
    {
        return $this->driverRepository->delete($id);
    }

    public function getDriversByOrder(string $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return collect();
        }

        $drivers = Driver::whereHas('warehouses', function ($query) use ($order) {
            $query->where('driver_warehouses.warehouse_id', $order->warehouse_id);
        })->get();

        return $drivers;
    }
}