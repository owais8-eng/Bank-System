<?php

namespace App\Domain\Accounts\States;

use App\Domain\Accounts\States\AccountState;
use App\Models\Account;
use Exception;

class ActiveState implements AccountState
{
    public function deposit(Account $account, float $amount): void
    {
        $account->increment('balance', $amount);
    }

    public function withdraw(Account $account, float $amount): void
    {
        if ($account->balance < $amount) {
            throw new Exception('Insufficient balance');
        }

        $account->decrement('balance', $amount);
    }

    public function canChangeStateTo(string $state): bool
    {
        return in_array($state, ['frozen', 'suspended', 'closed']);
    }
}
