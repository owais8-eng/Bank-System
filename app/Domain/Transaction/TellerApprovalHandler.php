<?php

namespace App\Domain\Transaction;

use App\Domain\Transaction\TransactionApprovalHandler as TransactionTransactionApprovalHandler;
use App\Models\Transaction;
use TransactionApprovalHandler;

class TellerApprovalHandler extends TransactionTransactionApprovalHandler
{
    public function handle(Transaction $transaction): void
    {
        if ($transaction->amount <= 5000) {
            $transaction->approve('teller');
            return;
        }

        parent::handle($transaction);
    }
}
