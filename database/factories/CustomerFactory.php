<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'document' => $this->faker->unique()->numerify('##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'notes' => $this->faker->boolean(20) ? $this->faker->sentence() : null,
            'last_purchase_at' => $this->faker->dateTimeBetween('-8 months', 'now'),
            'archived_at' => null,
        ];
    }
}
