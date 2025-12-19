<?php


namespace App\Strategies\Interest;

interface InterestStrategy
{
    public function calculate(float $balance):  float;
}
