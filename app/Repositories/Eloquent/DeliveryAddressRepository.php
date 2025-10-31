<?php

namespace App\Repositories\Eloquent;

use App\Models\UserDeliveryAddress;
use App\Repositories\Contracts\DeliveryAddressRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class DeliveryAddressRepository extends BaseRepository implements DeliveryAddressRepositoryInterface
{
    public function __construct(UserDeliveryAddress $model)
    {
        $this->model = $model;
    }
    // Custom methods for Region if needed
}
