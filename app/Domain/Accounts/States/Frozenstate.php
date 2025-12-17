<?php

namespace App\Domain\Accounts\States;

use App\Domain\Accounts\States\AccountState;
use App\Models\Account;
use Exception;

class FrozenState implements AccountState
{
    public function deposit(Account $account, float $amount): void
    {
        throw new Exception('Frozen account cannot accept deposits');
    }

    public function withdraw(Account $account, float $amount): void
    {
        throw new Exception('Frozen account cannot withdraw');
    }

    public function canChangeStateTo(string $state): bool
    {
        return in_array($state, ['active', 'closed']);
    }
}
