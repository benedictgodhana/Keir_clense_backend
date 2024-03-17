<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Assign admin role
        $adminRole = Role::where('name', 'admin')->first();
        $admin->assignRole($adminRole);

        // Create a member user
        $customer= User::create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
        ]);

        // Assign member role
        $customerRole = Role::where('name', 'customer')->first();
        $customer->assignRole($customerRole);


        $employee = User::create([
            'name' => 'Employee',
            'email' => 'employee@example.com',
            'password' => bcrypt('password'),
        ]);

        // Assign member role
        $employeeRole = Role::where('name', 'employee')->first();
        $employee->assignRole($employeeRole);
    }
    }

