<?php

namespace App\Domain\Accounts\Decorator;

use DomainException;

class OverdraftProtectionDecorator extends AccountAuthorizationDecorator
{
    private float $limit;

    public function __construct(AccountAuthorization $account, float $limit)
    {
        parent::__construct($account);
        $this->limit = $limit;
    }

    public function authorizeWithdraw(float $amount): void
    {
        $available = $this->getBalance() + $this->limit;
        if ($amount > $available)  {
            throw new DomainException("Withdrawal denied: the requested amount ({$amount}) "
                . "exceeds the available balance ({$available})");
        }

        parent::authorizeWithdraw($amount);
    }
}
