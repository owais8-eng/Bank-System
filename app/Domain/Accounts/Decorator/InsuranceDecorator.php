<?php

declare(strict_types=1);

namespace App\Domain\Accounts\Decorator;

class InsuranceDecorator extends AccountDecorator
{
    public function getDescription(): string
    {
        return parent::getDescription().' + Insurance Coverage';
    }
}
