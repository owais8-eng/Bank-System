<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = Account::all();

        foreach ($accounts as $account) {
            // Create multiple transactions for each account
            $transactionCount = fake()->numberBetween(5, 20);

            for ($i = 0; $i < $transactionCount; $i++) {
                $type = fake()->randomElement(['deposit', 'withdrawal']);

                // For withdrawals, ensure account has sufficient balance
                $amount = $type === 'withdrawal'
                    ? fake()->randomFloat(2, 10, min(1000, $account->balance))
                    : fake()->randomFloat(2, 10, 2000);

                Transaction::factory()->create([
                    'account_id' => $account->id,
                    'user_id' => $account->user_id,
                    'type' => $type,
                    'amount' => $amount,
                    'status' => fake()->randomElement(['approved', 'pending', 'approved']),
                    'description' => fake()->optional()->sentence(),
                ]);

                // Update account balance based on transaction
                if ($type === 'deposit') {
                    $account->increment('balance', $amount);
                } elseif ($type === 'withdrawal' && $account->balance >= $amount) {
                    $account->decrement('balance', $amount);
                }
            }

            // Create some transfers between accounts
            $otherAccounts = Account::where('user_id', $account->user_id)
                ->where('id', '!=', $account->id)
                ->get();

            if ($otherAccounts->isNotEmpty()) {
                $transferCount = fake()->numberBetween(1, 3);

                for ($i = 0; $i < $transferCount; $i++) {
                    $toAccount = $otherAccounts->random();
                    $amount = fake()->randomFloat(2, 10, min(500, $account->balance));

                    if ($account->balance >= $amount) {
                        Transaction::factory()->create([
                            'account_id' => $account->id,
                            'user_id' => $account->user_id,
                            'type' => 'transfer',
                            'amount' => $amount,
                            'to_account_id' => $toAccount->id,
                            'status' => 'approved',
                            'description' => 'Transfer to '.$toAccount->nickname,
                        ]);

                        // Update balances
                        $account->decrement('balance', $amount);
                        $toAccount->increment('balance', $amount);
                    }
                }
            }
        }
    }
}
