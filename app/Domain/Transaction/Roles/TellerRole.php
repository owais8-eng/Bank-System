<?php

namespace App\Domain\Transaction\Roles;

use App\Domain\Transaction\RoleStrategy;

class TellerRole implements RoleStrategy
{
    public function canApproveTransaction(float $amount): bool
    {
        return $amount <= 1000;
    }

}
