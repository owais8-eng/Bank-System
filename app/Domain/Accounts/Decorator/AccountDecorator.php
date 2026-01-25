<?php

declare(strict_types=1);

namespace App\Domain\Accounts\Decorator;

abstract class AccountDecorator implements Account
{
    protected Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function getBalance(): float
    {
        return $this->account->getBalance();
    }

    public function withdraw(float $amount): bool
    {
        return $this->account->withdraw($amount);
    }

    public function getDescription(): string
    {
        return $this->account->getDescription();
    }
}
