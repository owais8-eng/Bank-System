<?php

declare(strict_types=1);

namespace App\Strategies\Interest;

class SavingsInterestStrategy implements InterestStrategy
{
    public function calculate(float $balance): float
    {
        return $balance * 0.03;
    }
}
