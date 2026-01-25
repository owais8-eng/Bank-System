<?php

declare(strict_types=1);

namespace App\Domain\Accounts\Decorator;

interface Account
{
    public function getBalance(): float;

    public function getDescription(): string;

    public function withdraw(float $amount): bool;
}
