<?php

namespace App\Domain\Accounts\Decorator;


class PremiumDecorator extends AccountDecorator
{
    public function getDescription(): string
    {
        return parent::getDescription() . " + Premium Services";
    }

    public function getBalance(): float
    {
        return parent::getBalance() + 100;
    }
}
