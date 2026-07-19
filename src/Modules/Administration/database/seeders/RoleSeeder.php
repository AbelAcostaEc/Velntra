<?php

namespace Modules\Administration\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin role (All permissions)
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $adminRole->syncPermissions(Permission::all());

        // Seller role (Operational permissions)
        $sellerRole = Role::firstOrCreate([
            'name' => 'seller',
            'guard_name' => 'web',
        ]);
        $sellerRole->syncPermissions([
            'dashboard.view',
            'categories.view',
            'products.view',
            'customers.view',
            'customers.create',
            'customers.update',
            'sales.view',
            'sales.create',
            'sales.cancel',
        ]);
    }
}
