<?php

namespace App\Repositories\Eloquent;

use Spatie\Permission\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function createRole(array $data): Role
    {
        $role = Role::create(['name' => $data['name'], 'description' => $data['description']]);
        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }
        return $role;
    }

    public function update($id, array $data): Role
    {
        $role = $this->find($id);
        $role->update(['name' => $data['name'], 'description' => $data['description']]);
        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }
        return $role;
    }

    public function search($searchFor)
    {
        return $this->model
        ->where(function ($q) use ($searchFor) {
            $q->where('name', 'like', '%' . $searchFor . '%');
        })
        ->paginate(10);
    }
}
