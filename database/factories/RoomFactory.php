<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        $types = ['Consultation', 'Surgery', 'Recovery', 'Imaging', 'Lab', 'Physical Therapy'];

        return [
            'name' => 'Room ' . $this->faker->unique()->numberBetween(101, 999),
            'room_number' => $this->faker->numberBetween(101, 999),
            'type' => $this->faker->randomElement($types),
            'capacity' => $this->faker->numberBetween(1, 4),
            'equipment' => $this->faker->words(5, true),
            'description' => $this->faker->sentence(),
            'is_available' => $this->faker->boolean(80),
            'hourly_rate' => $this->faker->numberBetween(20, 150),
        ];
    }
}
