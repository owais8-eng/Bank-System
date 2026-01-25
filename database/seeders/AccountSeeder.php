<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create accounts for existing users
        $users = User::all();

        foreach ($users as $user) {
            // Create savings account
            Account::factory()->create([
                'user_id' => $user->id,
                'type' => 'savings',
                'balance' => fake()->randomFloat(2, 1000, 50000),
                'nickname' => 'Primary Savings',
            ]);

            // Create checking account
            Account::factory()->create([
                'user_id' => $user->id,
                'type' => 'checking',
                'balance' => fake()->randomFloat(2, 500, 10000),
                'nickname' => 'Checking Account',
            ]);

            // Randomly create additional accounts
            if (fake()->boolean(30)) { // 30% chance
                Account::factory()->create([
                    'user_id' => $user->id,
                    'type' => fake()->randomElement(['loan', 'investment']),
                    'balance' => fake()->randomFloat(2, -50000, 100000), // Can be negative for loans
                ]);
            }
        }

        // Create some account hierarchies (parent-child relationships)
        $parentAccounts = Account::where('type', 'savings')->take(3)->get();

        foreach ($parentAccounts as $parent) {
            Account::factory()->create([
                'user_id' => $parent->user_id,
                'type' => 'checking',
                'parent_id' => $parent->id,
                'balance' => fake()->randomFloat(2, 100, 5000),
                'nickname' => 'Sub-account of '.$parent->nickname,
            ]);
        }
    }
}
