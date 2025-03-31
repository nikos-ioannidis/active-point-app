<?php

namespace Database\Seeders;

use App\Enums\EmployeeIrataLevelEnum;
use App\Models\Employee;
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

        // Create employees for the test users
        if (!$user->employee) {
            Employee::create([
                'user_id' => $user->id,
                'employee_code' => 'AD0001',
                'employee_name' => $user->name,
                'job_title' => 'Administrator',
                'phone_number' => '123-456-7890',
                'is_active' => true,
                'owns_equipment' => false,
                'is_contractor' => false,
                'irata_level' => EmployeeIrataLevelEnum::LEVEL_3->value,
            ]);
        }

        if (!$technician->employee) {
            Employee::create([
                'user_id' => $technician->id,
                'employee_code' => 'TE0001',
                'employee_name' => $technician->name,
                'job_title' => 'Senior Technician',
                'phone_number' => '098-765-4321',
                'is_active' => true,
                'owns_equipment' => true,
                'is_contractor' => true,
                'irata_level' => EmployeeIrataLevelEnum::LEVEL_2->value,
            ]);
        }

        // Create some additional employees without users
        Employee::factory(5)->create();
    }
}
