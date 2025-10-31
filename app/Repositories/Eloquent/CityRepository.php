<?php

namespace App\Repositories\Eloquent;

use App\Models\City;
use App\Repositories\Contracts\CityRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class CityRepository extends BaseRepository implements CityRepositoryInterface
{
    public function __construct(City $model)
    {
        $this->model = $model;
    }

    public function getCitiesByRegion(string $regionId, string $type = 'paginated')
    {
        $cities = $this->model->where('region_id', $regionId)->orderBy('name', 'asc');
        return $type == 'paginated' ? 
            $cities->paginate(10) :
            $cities->get();
    }

    public function search(string $name, string $regionId)
    {
        return $this->model->where('region_id', $regionId)
        ->where(function ($q) use ($name) {
            $q->where('name', 'like', '%' . $name . '%')
              ->orWhereHas('region', function ($query) use ($name) {
                  $query->where('name', 'like', '%' . $name . '%');
              });
        })
        ->paginate(10);
    }

    // Custom methods for Region if needed
}
