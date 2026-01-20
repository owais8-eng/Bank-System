<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use App\Domain\Transaction\TransactionApprovalHandler as TransactionTransactionApprovalHandler;
use App\Models\Transaction;

class RejectApprovalHandler extends TransactionTransactionApprovalHandler
{
    public function handle(Transaction $transaction): void
    {
        throw new \DomainException('Transaction amount exceeds approval limits');
    }
}
