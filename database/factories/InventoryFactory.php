<?php

namespace Database\Factories;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventory>
 */
class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition(): array
    {
        $categories = ['Medicine', 'Equipment', 'Supplies', 'Surgical', 'Diagnostic'];
        $units = ['piece', 'box', 'bottle', 'pack', 'vial', 'tablet'];

        return [
            'name' => $this->faker->word() . ' ' . $this->faker->word(),
            'item_code' => $this->faker->unique()->bothify('INV-#####-??'),
            'category' => $this->faker->randomElement($categories),
            'description' => $this->faker->sentence(),
            'quantity' => $this->faker->numberBetween(0, 500),
            'reorder_level' => $this->faker->numberBetween(5, 50),
            'unit_price' => $this->faker->randomFloat(2, 5, 500),
            'unit' => $this->faker->randomElement($units),
            'expiry_date' => $this->faker->dateTimeBetween('+1 month', '+1 year'),
            'last_restocked' => $this->faker->dateTime(),
            'supplier' => $this->faker->company(),
            'status' => $this->faker->randomElement(['in_stock', 'low_stock', 'out_of_stock']),
        ];
    }
}
