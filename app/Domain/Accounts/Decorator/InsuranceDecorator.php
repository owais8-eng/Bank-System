<?php

namespace App\Domain\Accounts\Decorator;



class InsuranceDecorator extends AccountDecorator
{
    public function getDescription(): string
    {
        return parent::getDescription() . " + Insurance Coverage";
    }
}
