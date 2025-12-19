<?php

namespace App\Domain\Accounts\Decorator;

use App\Models\Account;
use DomainException;

class BaseAccountAuthorization implements AccountAuthorization
{
    protected Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function authorizeWithdraw(float $amount): void
    {

    }

    public function getBalance(): float
    {
        return $this->account->balance;
    }
}
