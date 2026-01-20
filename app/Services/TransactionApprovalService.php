<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Transaction\AutoApprovalHandler;
use App\Domain\Transaction\ManagerApprovalHandler;
use App\Domain\Transaction\RejectApprovalHandler;
use App\Domain\Transaction\TellerApprovalHandler;
use App\Models\Transaction;

/**
 * Transaction Approval Service using Chain of Responsibility Pattern
 * Builds and executes the approval chain for transactions
 */
class TransactionApprovalService
{
    /**
     * Approve transaction using Chain of Responsibility pattern
     */
    public function approve(Transaction $transaction): string
    {
        // Build the approval chain
        $chain = $this->buildApprovalChain();

        // Execute the chain
        $chain->handle($transaction);

        // Return the approval type that was set
        return $transaction->approved_type ?? 'pending';
    }

    /**
     * Build the approval chain based on transaction amount
     * Chain: Auto -> Teller -> Manager -> Reject
     */
    private function buildApprovalChain()
    {
        $autoHandler = new AutoApprovalHandler();
        $tellerHandler = new TellerApprovalHandler();
        $managerHandler = new ManagerApprovalHandler();
        $rejectHandler = new RejectApprovalHandler();

        // Build the chain
        $autoHandler->setNext($tellerHandler)
            ->setNext($managerHandler)
            ->setNext($rejectHandler);

        return $autoHandler;
    }
}
