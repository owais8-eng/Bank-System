<?php

namespace App\Domain\Transaction;

use App\Domain\Transaction\TransactionApprovalHandler as TransactionTransactionApprovalHandler;
use App\Models\Transaction;
use TransactionApprovalHandler;

class ManagerApprovalHandler extends TransactionTransactionApprovalHandler
{
   public function handle(Transaction $transaction): void
    {
        if ($transaction->amount <= 20000) {
            $transaction->approve('manager');
            return;
        }

        parent::handle($transaction);
    }

}
