<?php

declare(strict_types=1);

namespace App\Domain\Accounts\States;

use App\Models\Account;

interface AccountState
{
    public function deposit(Account $account, float $amount): void;

    public function withdraw(Account $account, float $amount): void;

    public function canChangeStateTo(string $state): bool;
}
