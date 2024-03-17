<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $customerRole = Role::create(['name' => 'customer']);
        $serviceProviderRole = Role::create(['name' => 'employee']);

        // Define permissions for admin
        $adminPermissions = [
            'create_booking',
            'edit_booking',
            'delete_booking',
            'view_booking',
            'manage_service_providers',
            'manage_users',
            'view_reports',
        ];

        // Assign admin permissions
        foreach ($adminPermissions as $permission) {
            Permission::create(['name' => $permission]);
            $adminRole->givePermissionTo($permission);
        }

        // Define permissions for customer
        $customerPermissions = [
            'create_booking',
            'edit_booking',
            'view_booking',
        ];

        // Assign customer permissions
        foreach ($customerPermissions as $permission) {
            Permission::create(['name' => $permission]);
            $customerRole->givePermissionTo($permission);
        }

        // Define permissions for service provider
        $serviceProviderPermissions = [
            'view_booking',
        ];

        // Assign service provider permissions
        foreach ($serviceProviderPermissions as $permission) {
            Permission::create(['name' => $permission]);
            $serviceProviderRole->givePermissionTo($permission);
        }
    }
}
