<?php

namespace Database\Factories;

use App\Enums\EmployeeIrataLevelEnum;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'employee_code' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{4}'),
            'employee_name' => $this->faker->name(),
            'job_title' => $this->faker->jobTitle(),
            'phone_number' => $this->faker->phoneNumber(),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
            'owns_equipment' => $this->faker->boolean(30), // 30% chance of owning equipment
            'irata_level' => $this->faker->randomElement(EmployeeIrataLevelEnum::values()),
        ];
    }

    /**
     * Indicate that the employee has a user.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withUser(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => User::factory(),
            ];
        });
    }
}
