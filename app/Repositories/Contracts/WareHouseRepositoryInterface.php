<?php

namespace App\Repositories\Contracts;

interface WareHouseRepositoryInterface extends BaseRepositoryInterface
{
    // Add region-specific methods later
    public function search(string $name);

    public function getWarehousesByRegion(string $regionId);
}
