<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        $specializations = [
            'General Practice',
            'Cardiology',
            'Orthopedics',
            'Dermatology',
            'Pediatrics',
            'Neurology',
            'Psychiatry',
            'Gynecology',
            'Surgery',
            'Urology'
        ];

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'specialization' => $this->faker->randomElement($specializations),
            'license_number' => $this->faker->unique()->bothify('LIC-#####-??'),
            'hourly_rate' => $this->faker->numberBetween(50, 300),
            'bio' => $this->faker->paragraph(),
            'qualifications' => $this->faker->paragraph(),
            'experience_years' => $this->faker->numberBetween(1, 40),
            'status' => $this->faker->randomElement(['active', 'inactive', 'on_leave']),
            'shift_start' => '09:00',
            'shift_end' => '17:00',
            'working_days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        ];
    }
}
