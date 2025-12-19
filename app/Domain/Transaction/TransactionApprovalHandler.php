<?php

namespace App\Domain\Transaction;

use App\Domain\Transaction\InterfaceApprovalHandler;
use App\Models\Transaction;

abstract class TransactionApprovalHandler implements InterfaceApprovalHandler
{
    protected ?TransactionApprovalHandler $next = null;

    public function setNext(InterfaceApprovalHandler $handler): self
    {
        $this->next = $handler;
        return $handler;
    }

public function handle(Transaction $transaction): void
    {
        if ($this->next) {
            $this->next->handle($transaction);
        }
    }}


