<?php

namespace App\Domain\Transaction;

use App\Domain\Transaction\TransactionApprovalHandler as TransactionTransactionApprovalHandler;
use App\Models\Transaction;
use TransactionApprovalHandler;

class RejectApprovalHandler extends TransactionTransactionApprovalHandler
{
    public function handle(Transaction $transaction): void
    {
        throw new \DomainException('Transaction amount exceeds approval limits');
    }
}
