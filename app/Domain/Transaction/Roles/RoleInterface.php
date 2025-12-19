<?php

namespace App\Domain\Transaction\Roles;


interface RoleInterface
{
    public function canApproveTransaction(float $amount): bool;

}
