<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name) . '-' . Str::lower($this->faker->unique()->bothify('??##??')),
            'description' => $this->faker->optional()->sentence(8),
            'type' => $this->faker->randomElement(['product', 'service']),
            'sector' => $this->faker->randomElement(['papeleria', 'impresion', 'diseno']),
            'sale_price' => $this->faker->randomFloat(2, 1000, 150000),
            'cost' => $this->faker->randomFloat(2, 500, 120000),
            'stock' => $this->faker->numberBetween(0, 250),
            'min_stock' => $this->faker->numberBetween(0, 25),
            'unit' => $this->faker->optional()->randomElement(['unidad', 'paquete', 'servicio', 'kit']),
            'featured' => $this->faker->boolean,
            'active' => $this->faker->boolean(85),
        ];
    }
}
