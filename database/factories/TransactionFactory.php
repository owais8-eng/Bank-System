<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\User;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'account_id'    => Account::factory(),   // إنشاء حساب وهمي
            'user_id'       => User::factory(),      // إنشاء مستخدم وهمي
            'type'          => $this->faker->randomElement(['deposit','withdrawal','transfer']),
            'amount'        => $this->faker->randomFloat(2, 1, 1000),
            'status'        => 'pending',
            'description'   => $this->faker->sentence,
            'to_account_id' => Account::factory(),
        ];
    }
}
