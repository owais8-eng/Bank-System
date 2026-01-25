<?php

declare(strict_types=1);

namespace App\Domain\Accounts\Decorator;

class OverdraftDecorator extends AccountDecorator
{
    private float $limit;

    public function __construct(Account $account, float $limit)
    {
        parent::__construct($account);
        $this->limit = $limit;
    }

    public function withdraw(float $amount): bool
    {
        $available = $this->getBalance() + $this->limit;

        if ($amount > $available) {
            return false;
        }

        return true;
    }
}
