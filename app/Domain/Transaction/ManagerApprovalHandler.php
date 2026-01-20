<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use App\Domain\Transaction\TransactionApprovalHandler as TransactionTransactionApprovalHandler;
use App\Models\Transaction;

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
