<?php

declare(strict_types=1);

namespace App\Strategies\Interest;

interface InterestStrategy
{
    public function calculate(float $balance): float;
}
