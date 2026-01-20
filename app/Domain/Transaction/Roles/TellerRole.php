<?php

declare(strict_types=1);

namespace App\Domain\Transaction\Roles;

class TellerRole extends BaseRole
{
    public function canApproveTransaction(float $amount): bool
    {
        return $amount <= 5000;
    }
}
