<?php

declare(strict_types=1);

namespace App\Domain\Accounts\States;

use App\Models\Account;
use Exception;

class ActiveState implements AccountState
{
    public function deposit(Account $account, float $amount): void
    {
        $account->balance += $amount;
        $account->save();
    }

    public function withdraw(Account $account, float $amount): void
    {
        if ($account->balance < $amount) {
            throw new Exception('Insufficient balance');
        }

        $account->balance -= $amount;
        $account->save();
    }

    public function canChangeStateTo(string $state): bool
    {
        return in_array($state, ['frozen', 'suspended', 'closed']);
    }
}
