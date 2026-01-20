<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'account_id' => Account::factory(),   // إنشاء حساب وهمي
            'user_id' => User::factory(),      // إنشاء مستخدم وهمي
            'type' => $this->faker->randomElement(['deposit', 'withdrawal', 'transfer']),
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'status' => 'pending',
            'description' => $this->faker->sentence,
            'to_account_id' => Account::factory(),
        ];
    }
}
