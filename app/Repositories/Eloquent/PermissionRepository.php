<?php

namespace App\Repositories\Eloquent;

use Spatie\Permission\Models\Permission;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class PermissionRepository extends BaseRepository implements PermissionRepositoryInterface
{
    public function __construct(Permission $model)
    {
        $this->model = $model;
    }

    // Custom methods for Region if needed
}
