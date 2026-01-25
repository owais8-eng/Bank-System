<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Transaction\AutoApprovalHandler;
use App\Domain\Transaction\ManagerApprovalHandler;
use App\Domain\Transaction\RejectApprovalHandler;
use App\Domain\Transaction\TellerApprovalHandler;
use App\Models\Transaction;

class TransactionApprovalService
{
    public function approve(Transaction $transaction): string
    {
        $chain = $this->buildApprovalChain();

        $chain->handle($transaction);

        return $transaction->approved_type ?? 'pending';
    }

    private function buildApprovalChain()
    {
        $autoHandler = new AutoApprovalHandler;
        $tellerHandler = new TellerApprovalHandler;
        $managerHandler = new ManagerApprovalHandler;
        $rejectHandler = new RejectApprovalHandler;

        $autoHandler->setNext($tellerHandler)
            ->setNext($managerHandler)
            ->setNext($rejectHandler);

        return $autoHandler;
    }
}
