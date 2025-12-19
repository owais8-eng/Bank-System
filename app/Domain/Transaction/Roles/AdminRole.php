<?php

namespace App\Domain\Transaction\Roles;

class AdminRole extends BaseRole
{
     public function canApproveTransaction(float $amount): bool
    {
        return true;
    }
}
