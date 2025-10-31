<?php

namespace App\Repositories\Eloquent;

use App\Models\Driver;
use App\Repositories\Contracts\DriverRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class DriverRepository extends BaseRepository implements DriverRepositoryInterface
{
    public function __construct(Driver $model)
    {
        $this->model = $model;
    }
    // Custom methods for Region if needed
}
