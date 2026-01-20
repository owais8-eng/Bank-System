<?php

declare(strict_types=1);

namespace App\Strategies\Interest\InterestStrategy;

use App\Strategies\Interest\InterestStrategy;

class InterestCalculator
{
    private InterestStrategy $strategy;

    public function __construct(InterestStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function calculate(float $balance): float
    {
        return $this->strategy->calculate($balance);

    }
}
