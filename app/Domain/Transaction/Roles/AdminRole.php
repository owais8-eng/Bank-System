<?php

namespace App\Domain\Transaction\Roles;

use App\Domain\Transaction\Roles\RoleStrategy;
use App\Domain\Transaction\RoleStrategy as TransactionRoleStrategy;

class AdminRole implements TransactionRoleStrategy
{
    public function canApproveTransaction(float $amount): bool
    {
        return true;
    }
}

