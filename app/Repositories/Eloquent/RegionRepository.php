<?php

namespace App\Repositories\Eloquent;

use App\Models\Region;
use App\Models\City;
use App\Repositories\Contracts\RegionRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class RegionRepository extends BaseRepository implements RegionRepositoryInterface
{
    public function __construct(Region $model)
    {
        $this->model = $model;
    }

    public function search(string $name)
    {
        return $this->model
        ->where(function ($q) use ($name) {
            $q->where('name', 'like', '%' . $name . '%');
        })
        ->paginate(10);
    }

    public function getCitiesByRegion(string $regionId)
    {
        return City::
        where('region_id', $regionId)
        ->paginate(10);
    }

    // Custom methods for Region if needed
}
