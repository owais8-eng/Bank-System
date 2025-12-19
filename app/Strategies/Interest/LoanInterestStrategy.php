<?php

namespace App\Strategies\Interest;

class LoanInterestStrategy implements InterestStrategy
{

    public function calculate(float $balance): float
    {
        return $balance * 0.07;
    }
    
}
