<?php

namespace App\Repositories\Contracts;

interface RegionRepositoryInterface extends BaseRepositoryInterface
{
    // Add region-specific methods later
    public function search(string $name);
    public function getCitiesByRegion(string $regionId);
}
