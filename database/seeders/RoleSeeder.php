<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $technicianRole = Role::firstOrCreate(['name' => 'Technician']);

        // You can also create permissions and assign them to roles if needed
        // For example:
        // $manageUsersPermission = Permission::firstOrCreate(['name' => 'manage users']);
        // $adminRole->givePermissionTo($manageUsersPermission);
    }
}
