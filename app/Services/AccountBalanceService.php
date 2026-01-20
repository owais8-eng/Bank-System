<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;

class AccountBalanceService
{
    public function updateBalance(Account $account, float $amount): float
    {
        $oldBalance = $account->balance;
        $account->balance += $amount;
        $account->save();

        return $oldBalance;
    }

    public function incrementBalance(Account $account, float $amount): float
    {
        $oldBalance = $account->balance;
        // @phpstan-ignore-next-line
        $account->increment('balance', $amount);

        return $oldBalance;
    }

    public function decrementBalance(Account $account, float $amount): float
    {
        $oldBalance = $account->balance;
        // @phpstan-ignore-next-line
        $account->decrement('balance', $amount);

        return $oldBalance;
    }

    public function getOldBalance(Account $account): float
    {
        return $account->getOriginal('balance') ?? $account->balance;
    }
}
