<?php

declare(strict_types=1);

namespace App\Domain\Transaction\Roles;

interface RoleInterface
{
    public function canApproveTransaction(float $amount): bool;
}
