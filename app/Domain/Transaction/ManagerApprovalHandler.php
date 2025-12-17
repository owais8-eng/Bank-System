<?php

namespace App\Domain\Transaction;

use TransactionApprovalHandler;

class ManagerApprovalHandler extends TransactionApprovalHandler
{
    public function handle(float $amount): string
    {
        if ($amount <= 5000) {
            return 'pending_manager';
        }

        return 'pending_admin';
    }
}
