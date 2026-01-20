<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use App\Domain\Transaction\TransactionApprovalHandler as TransactionTransactionApprovalHandler;
use App\Models\Transaction;

interface InterfaceApprovalHandler
{
    public function setNext(InterfaceApprovalHandler $handler): TransactionTransactionApprovalHandler;

    public function handle(Transaction $transaction): void;
}
