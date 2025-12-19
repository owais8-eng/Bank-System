<?php

namespace App\Domain\Transaction\Roles;


class ManagerRole extends BaseRole {
    public function canApproveTransaction(float $amount): bool { return $amount <= 20000; }
}
