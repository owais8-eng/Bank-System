<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Account;
use App\Models\RecurringTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class RecurringTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'customer')->get();

        foreach ($users as $user) {
            $userAccounts = Account::where('user_id', $user->id)->get();

            if ($userAccounts->isEmpty()) {
                continue;
            }

            // Create recurring transactions for some users
            $recurringCount = fake()->numberBetween(0, 2); // 0-2 recurring transactions per user

            for ($i = 0; $i < $recurringCount; $i++) {
                $account = $userAccounts->random();

                RecurringTransaction::factory()->create([
                    'account_id' => $account->id,
                    'user_id' => $user->id,
                    'type' => fake()->randomElement(['deposit', 'withdrawal']),
                ]);
            }
        }
    }
}
