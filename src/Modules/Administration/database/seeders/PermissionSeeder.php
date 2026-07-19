<?php

namespace Modules\Administration\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions grouped by module
        $permissionsByModule = [
            'dashboard' => [
                'dashboard.view',
            ],
            'users' => [
                'users.view',
                'users.create',
                'users.update',
                'users.delete',
            ],
            'roles' => [
                'roles.view',
                'roles.create',
                'roles.update',
                'roles.delete',
            ],
            'categories' => [
                'categories.view',
                'categories.create',
                'categories.update',
                'categories.delete',
            ],
            'products' => [
                'products.view',
                'products.create',
                'products.update',
                'products.delete',
            ],
            'customers' => [
                'customers.view',
                'customers.create',
                'customers.update',
                'customers.delete',
            ],
            'sales' => [
                'sales.view',
                'sales.create',
                'sales.update',
                'sales.delete',
                'sales.cancel',
            ],
            'settings' => [
                'settings.view',
                'settings.update',
            ],
        ];

        // Create permissions
        foreach ($permissionsByModule as $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web',
                ]);
            }
        }
    }
}


