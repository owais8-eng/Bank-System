<?php

declare(strict_types=1);

namespace App\Domain\Accounts\States;

use App\Models\Account;
use Exception;

class ClosedState implements AccountState
{
    public function deposit(Account $account, float $amount): void
    {
        throw new Exception('Closed account cannot accept deposits');
    }

    public function withdraw(Account $account, float $amount): void
    {
        throw new Exception('Closed account cannot withdraw');
    }

    public function canChangeStateTo(string $state): bool
    {
        return false; // final state
    }
}
