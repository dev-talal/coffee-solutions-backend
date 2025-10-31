<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Spatie\Permission\Models\Role;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function getUsersByRole(string $role, bool $exclude = false, string $searchFor = '', array $wareHouses = [])
    {
        return $this->model
            ->when($searchFor, function ($query) use ($searchFor) {
                $query->where(function ($q) use ($searchFor) {
                    $q->where('first_name', 'like', '%' . $searchFor . '%')
                    ->orWhere('last_name', 'like', '%' . $searchFor . '%')
                    ->orWhere('email', 'like', '%' . $searchFor . '%')
                    ->orWhere('phone', 'like', '%' . $searchFor . '%')
                    ->orWhere('location', 'like', '%' . $searchFor . '%')
                    ->orWhereHas('warehouses', function ($query) use ($searchFor) {
                        $query->where('name', 'like', '%' . $searchFor . '%');
                    });
                });
            })
            ->when($exclude, function ($query) use ($role) {
                $query->whereDoesntHave('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            }, function ($query) use ($role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })
            ->when($exclude, function ($query) use ($wareHouses) {
                $query->when($wareHouses, function ($query) use ($wareHouses) {
                     $query->whereHas('warehouses', function ($q) use ($wareHouses) {
                        $q->withTrashed()->whereIn('ware_houses.id', $wareHouses);
                    });
                });
            }, function ($query) use ($wareHouses) {
                    $query->when($wareHouses, function ($query) use ($wareHouses) {
                        $query->whereIn('users.warehouse_id', $wareHouses);
                    });    
            })
            ->paginate(10);
    }

    public function getStaffByWarehouse(string $warehouseId, string $role = '')
    {
        return $this->model
            ->when($role, function ($query) use ($role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            })
            ->whereHas('warehouses', function ($query) use ($warehouseId) {
                $query->where('staff_warehouses.warehouse_id', $warehouseId);
            })
            ->select('id', 'first_name', 'last_name', 'email', 'created_at')
            ->get();
    }
}
