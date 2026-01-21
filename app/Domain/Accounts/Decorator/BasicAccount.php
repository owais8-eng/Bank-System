<?php

declare(strict_types=1);

namespace App\Domain\Accounts\Decorator;


class BasicAccount implements Account
{
    protected float $balance;

    public function __construct(float $balance)
    {
        $this->balance = $balance;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function withdraw(float $amount): bool
    {
        if ($amount > $this->balance) {
            return false;
        }

        $this->balance -= $amount;
        return true;
    }

    public function getDescription(): string
    {
        return "Basic Bank Account";
    }
}
