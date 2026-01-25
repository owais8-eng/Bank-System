<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Payments\PaymentGatewayFactory;
use App\Models\Account;
use App\Models\Transaction;

class TransactionService
{
    public function __construct(
        private DepositService $depositService,
        private WithdrawalService $withdrawalService,
        private TransferService $transferService
    ) {
    }

    public function deposit( $account, float $amount,string $gateway, ?string $description = null): Transaction
    {
        $transaction = $this->depositService->deposit($account, $amount, $description);
        $paymentGateway = PaymentGatewayFactory::make($gateway);

        if (! $paymentGateway->process($transaction)) {
            throw new \DomainException('Payment gateway failed');
        }

        return $transaction;

    }

    public function withdraw(Account $account, float $amount, ?string $description = null): Transaction
    {
        return $this->withdrawalService->withdraw($account, $amount, $description);
    }

    public function transfer(Account $from, Account $to, float $amount, ?string $description = null): Transaction
    {
        return $this->transferService->transfer($from, $to, $amount, $description);
    }

    public function approve(Transaction $transaction, string $type): void
    {
        $transaction->update([
            'status' => 'approved',
            'approval_type' => $type,
        ]);
    }
}
