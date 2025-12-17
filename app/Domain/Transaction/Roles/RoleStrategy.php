<?php

namespace App\Domain\Transaction;

interface RoleStrategy
{
    public function canApproveTransaction(float $amount): bool;
}

