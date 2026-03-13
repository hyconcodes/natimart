<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage state coordinators',
            'disable state coordinators',
            'approve vendors',
            'approve products',
            'manage vendors',
            'manage products',
            'view roles',
            'manage roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Master Admin
        $role = Role::firstOrCreate(['name' => 'masteradmin']);
        $role->givePermissionTo(Permission::all());

        // State Coordinator
        $role = Role::firstOrCreate(['name' => 'state_coordinator']);
        $role->givePermissionTo([
            'approve vendors',
            'approve products',
            'manage vendors',
            'manage products',
        ]);

        // Vendor
        $role = Role::firstOrCreate(['name' => 'vendor']);
        // Vendors might have permissions to create products, etc. (future)

        // User
        $role = Role::firstOrCreate(['name' => 'user']);
    }
}
