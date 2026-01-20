<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['savings', 'checking', 'loan', 'investment']),
            'balance' => fake()->randomFloat(2, 0, 10000),
            'state' => 'active',
            'parent_id' => null,
            'nickname' => fake()->optional()->word(),
            'daily_limit' => fake()->optional()->randomFloat(2, 1000, 5000),
            'metadata' => null,
        ];
    }

    /**
     * Indicate that the account is a savings account.
     */
    public function savings(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'savings',
        ]);
    }

    /**
     * Indicate that the account is a checking account.
     */
    public function checking(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'checking',
        ]);
    }

    /**
     * Indicate that the account is frozen.
     */
    public function frozen(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => 'frozen',
        ]);
    }

    /**
     * Indicate that the account has a parent.
     */
    public function withParent(int $parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }
}
