<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use App\Models\Transaction;

abstract class TransactionApprovalHandler implements InterfaceApprovalHandler
{
    protected ?InterfaceApprovalHandler $next = null;

    public function setNext(InterfaceApprovalHandler $handler): TransactionApprovalHandler
    {
        $this->next = $handler;

        return $this;
    }

    public function handle(Transaction $transaction): void
    {
        if ($this->next) {
            $this->next->handle($transaction);
        }
    }
}
