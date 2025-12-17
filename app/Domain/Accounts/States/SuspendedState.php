<?php

namespace App\Domain\Accounts\States;

use App\Domain\Accounts\States\AccountState;
use App\Models\Account;
use Exception;

class SuspendedState implements AccountState
{
    public function deposit(Account $account, float $amount): void
    {
        throw new Exception('Suspended account cannot accept deposits');
    }

    public function withdraw(Account $account, float $amount): void
    {
        throw new Exception('Suspended account cannot withdraw');
    }

    public function canChangeStateTo(string $state): bool
    {
        return in_array($state, ['active', 'closed']);
    }
}
