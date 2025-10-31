<?php

namespace App\Http\Controllers\Api\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Http\Resources\RoleResource;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\RoleRequest;

class RoleController extends Controller
{
    use ApiResponseTrait;
    protected $roles;
    public function __construct(RoleRepositoryInterface $roles)
    {
        $this->roles = $roles;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'paginated'); // default to paginated
        $searchFor = $request->query('search', '');
        if(empty($searchFor)) {
            $roles = $this->roles;
            $roles = $type == 'paginated' ? 
            $roles->paginate(10) :
            $roles->all();
        }else{
            $roles = $this->roles->search($searchFor);
        }
        return $this->successCollection($roles, RoleResource::class, 'Roles retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        $role = $request->validated();
        $role = $this->roles->createRole($role);
        return $this->successResource($role, RoleResource::class, 'Role created successfully', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = $this->roles->find($id);
        if (!$role) {
            return $this->error('Role not found', 404);
        }
        return $this->successResource($role, RoleResource::class, 'Role retrieved successfully', 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, string $id)
    {
        $role = $request->validated();
        $role = $this->roles->update($id, $role);
        return $this->successResource($role, RoleResource::class, 'Role updated successfully', 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = $this->roles->find($id);
        if (!$role) {
            return $this->error('Role not found', 404);
        }

        if($role->is_editable == 0) {
            return $this->error('You can not delete this role', 403);
        }
        $this->roles->delete($id); 
        return $this->success(null, 'Role deleted successfully', 200);
    }
}
