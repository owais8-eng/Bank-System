<?php

namespace App\Services;

use App\Models\Transaction;

class TransactionApprovalService
{
    public function approve(Transaction $transaction): string
    {
        $approvalType = 'teller';

        if ($transaction->amount <= 1000) {
            $approvalType = 'auto';
        }
        elseif ($transaction->amount > 10000) {
            $approvalType = 'manager';
        }

        if (auth()->user()->role === 'admin') {
        $approvalType = 'admin';
    }

        $transaction->approve($approvalType);


        return $approvalType;
    }
}
