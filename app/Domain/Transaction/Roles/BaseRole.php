<?php

declare(strict_types=1);

namespace App\Domain\Transaction\Roles;

class BaseRole implements RoleInterface
{
    public function canApproveTransaction(float $amount): bool
    {
        return false;
    }
}
