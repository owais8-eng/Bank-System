<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Payments\PaymentGatewayFactory;
use App\Events\TransactionCreated;
use App\Models\Account;
use App\Models\Transaction;
use DomainException;
use Illuminate\Support\Facades\DB;

class WithdrawalService
{
    public function __construct(
        private AccountBalanceService $balanceService,
        private TransactionLoggerService $loggerService,
        private TransactionEventService $eventService,
        private PaymentGatewayFactory $gatewayFactory
    ) {}

    public function withdraw(Account $account, float $amount, ?string $description = null): Transaction
    {
        $this->validateAmount($amount);

        return DB::transaction(function () use ($account, $amount, $description) {
            $oldBalance = $this->balanceService->decrementBalance($account, $amount);

            $transaction = Transaction::create([
                'account_id' => $account->id,
                'user_id' => auth()->id(),
                'type' => 'withdrawal',
                'amount' => $amount,
                'status' => 'pending',
                'description' => $description,
            ]);

            $this->loggerService->logWithdrawal(
                $transaction,
                auth()->user(),
                $oldBalance,
                $account->fresh()->balance
            );

            $this->eventService->dispatchTransactionCreated($transaction);

            $gateway = $this->gatewayFactory->make('legacy');

            if (! $gateway->process($transaction)) {
                throw new DomainException('External payment failed');
            }

            $transaction->update(['status' => 'approved']);

            $this->eventService->dispatchTransactionCreated($transaction);


            event(new TransactionCreated($transaction));

            return $transaction;
        });


    }

    private function validateAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new DomainException('Amount must be positive');
        }
    }
}
