<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates two roles (admin, user) and basic permissions for demonstration.
     * Admin role gets all permissions, user role gets basic permissions.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view dashboard',
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view settings',
            'edit settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // User role (basic permissions)
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->givePermissionTo([
            'view dashboard',
            'view settings',
        ]);

        // Admin role (all permissions)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
    }
}
