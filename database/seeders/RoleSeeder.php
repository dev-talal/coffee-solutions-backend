<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Define roles
        $roles = ['admin', 'customer', 'finance', 'warehouse', 'sales', 'customer Care']; 

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'api',
                'is_editable' => 0, // Default to editable
            ]);

            // Assign all permissions to admin only
            if ($roleName == 'admin') {
                $permissions = Permission::all();
                $role->syncPermissions($permissions);
            }
        }
    }
}
