<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\TransactionCreated;
use App\Models\Transaction;

class TransactionEventService
{
    public function dispatchTransactionCreated(Transaction $transaction): void
    {
        event(new TransactionCreated($transaction));
    }
}
