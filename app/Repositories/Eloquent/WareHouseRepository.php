<?php

namespace App\Repositories\Eloquent;

use App\Models\WareHouse;
use App\Repositories\Contracts\WareHouseRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class WareHouseRepository extends BaseRepository implements WareHouseRepositoryInterface
{
    public function __construct(WareHouse $model)
    {
        $this->model = $model;
    }

    public function search(string $name)
    {
        return $this->model
        ->where(function ($q) use ($name) {
            $q->where('name', 'like', '%' . $name . '%')
              ->orWhereHas('region', function ($query) use ($name) {
                  $query->where('name', 'like', '%' . $name . '%');
              });
        })
        ->paginate(10);
    }

    public function getWarehousesByRegion(string $regionId)
    {
        return $this->model
        ->whereHas('warehouseRegions', function ($query) use ($regionId) {
            $query->where('warehouse_regions.region_id', $regionId);
        })
        ->get();
    }

}
