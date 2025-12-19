<?php

namespace App\Domain\Transaction\Roles;

class BaseRole implements RoleInterface {
    public function canApproveTransaction(float $amount): bool { return false; }
}
