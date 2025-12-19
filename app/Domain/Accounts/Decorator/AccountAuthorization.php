<?php

namespace App\Domain\Accounts\Decorator;

interface AccountAuthorization
{
    public function authorizeWithdraw(float $amount): void;
    public function getBalance(): float;
}
