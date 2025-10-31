<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'dashboard',
            'inbox',
            'cities',
            'regions',
            'customer categories',
            'product',
            'transactions',
            'roles',
            'staff',
            'warehouse',
            'product categories',
            'customers',
            'taxes',
            'promotions',
            'orders',
            'drivers',
            'product units'
        ];

        foreach ($modules as $module) {
            $permissionName = $module;
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'api'
            ]);
        }
    }
}
