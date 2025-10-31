<?php

namespace App\Services;

use App\Repositories\Contracts\WareHouseRepositoryInterface;

class WareHouseService
{
    protected WareHouseRepositoryInterface $wareHouseRepository;

    public function __construct(WareHouseRepositoryInterface $wareHouseRepository)
    {
        $this->wareHouseRepository = $wareHouseRepository;
    }

    public function paginate(int $perPage = 10)
    {
        return $this->wareHouseRepository->paginate($perPage);
    }

    public function all()
    {
        return $this->wareHouseRepository->all();
    }

    public function find(string $id)
    {
        return $this->wareHouseRepository->find($id);
    }

    public function create(array $data)
    {
        $regions = $data['region_ids'];
        unset($data['region_ids']);
        $warehouse = $this->wareHouseRepository->create($data);
        $this->saveRegions($regions, $warehouse);
        return $warehouse;
    }

    public function saveRegions($regions, $warehouse)
    {
        $warehouse->warehouseRegions()->detach();
        foreach ($regions as $regionId) {
            $warehouse->warehouseRegions()->attach($regionId);
        }
    }

    public function update(array $data, string $id)
    {
        $warehouse = $this->wareHouseRepository->find($id);
        if (!$warehouse) {
            return false;
        }
        $regions = $data['region_ids'];
        unset($data['region_ids']);
        $this->wareHouseRepository->update($id,$data);
        $this->saveRegions($regions, $warehouse);
        return $warehouse->fresh();
    }

    public function delete(string $id): bool
    {
        return $this->wareHouseRepository->delete($id);
    }

    public function search(string $name)
    {
        return $this->wareHouseRepository->search($name);
    }

    public function getWarehousesByRegion(string $regionId)
    {
        return $this->wareHouseRepository->getWarehousesByRegion($regionId);
    }
}