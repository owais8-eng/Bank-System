<?php

namespace App\Strategies\Interest;


use App\Strategies\Interest\InterestStrategy;

class SavingsInterestStrategy implements InterestStrategy
{

    public function calculate(float $balance): float
    {
        return $balance * 0.03;
    }
}
