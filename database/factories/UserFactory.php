<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'role' => User::ROLE_STAFF,
            'can_operate' => true,
            'can_inventory' => false,
            'active' => true,
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
            'can_operate' => false,
            'can_inventory' => false,
            'active' => true,
        ]);
    }

    public function operator(): static
    {
        return $this->staffOperate();
    }

    public function inventory(): static
    {
        return $this->staffInventory();
    }

    public function staffOperate(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_STAFF,
            'can_operate' => true,
            'can_inventory' => false,
            'active' => true,
        ]);
    }

    public function staffInventory(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_STAFF,
            'can_operate' => false,
            'can_inventory' => true,
            'active' => true,
        ]);
    }

    public function staffFull(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_STAFF,
            'can_operate' => true,
            'can_inventory' => true,
            'active' => true,
        ]);
    }
}
