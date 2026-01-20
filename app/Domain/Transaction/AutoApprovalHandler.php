<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use App\Domain\Transaction\TransactionApprovalHandler as TransactionTransactionApprovalHandler;
use App\Models\Transaction;

class AutoApprovalHandler extends TransactionTransactionApprovalHandler
{
    public function handle(Transaction $transaction): void
    {
        if ($transaction->amount <= 1000) {
            $transaction->approve('auto');

            return;
        }
        parent::handle($transaction);
    }
}
