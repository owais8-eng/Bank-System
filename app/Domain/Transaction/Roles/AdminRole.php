<?php

declare(strict_types=1);

namespace App\Domain\Transaction\Roles;

class AdminRole extends BaseRole
{
    public function canApproveTransaction(float $amount): bool
    {
        return true;
    }
}
