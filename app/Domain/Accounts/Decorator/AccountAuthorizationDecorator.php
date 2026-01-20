<?php

declare(strict_types=1);

namespace App\Domain\Accounts\Decorator;

abstract class AccountAuthorizationDecorator implements AccountAuthorization
{
    protected AccountAuthorization $account;

    public function __construct(AccountAuthorization $account)
    {
        $this->account = $account;
    }

    public function authorizeWithdraw(float $amount): void
    {
        $this->account->authorizeWithdraw($amount);
    }

    public function getBalance(): float
    {
        return $this->account->getBalance();
    }
}
