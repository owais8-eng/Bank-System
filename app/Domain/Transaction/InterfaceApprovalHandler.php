<?php

namespace App\Domain\Transaction;

use App\Domain\Transaction\TransactionApprovalHandler as TransactionTransactionApprovalHandler;
use App\Models\Transaction;
use TransactionApprovalHandler;

interface InterfaceApprovalHandler
{
     public function setNext(InterfaceApprovalHandler $handler): TransactionTransactionApprovalHandler;

    public function handle(Transaction $transaction): void;
}
