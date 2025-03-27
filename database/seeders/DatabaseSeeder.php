<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the role seeder first
        $this->call(RoleSeeder::class);

        // User::factory(10)->create();

        // Create a test user with Admin role if it doesn't exist
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Assign the Admin role to the test user if not already assigned
        if (!$user->hasRole('Admin')) {
            $user->assignRole('Admin');
        }

        // Create another test user with Technician role if it doesn't exist
        $technician = User::firstOrCreate(
            ['email' => 'technician@example.com'],
            [
                'name' => 'Test Technician',
                'password' => bcrypt('password'),
            ]
        );

        // Assign the Technician role to the technician user if not already assigned
        if (!$technician->hasRole('Technician')) {
            $technician->assignRole('Technician');
        }
    }
}
