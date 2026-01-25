<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use DomainException;
use Illuminate\Support\Facades\DB;

class DepositService
{
    public function __construct(
        private AccountBalanceService $balanceService,
        private TransactionLoggerService $loggerService,
        private TransactionEventService $eventService
    ) {}

    public function deposit(Account $account, float $amount, ?string $description = null): Transaction
    {
        $this->validateAmount($amount);

        return DB::transaction(function () use ($account, $amount, $description) {
            $oldBalance = $this->balanceService->incrementBalance($account, $amount);

            $transaction = Transaction::create([
                'account_id' => $account->id,
                'user_id' => auth()->id(),
                'type' => 'deposit',
                'amount' => $amount,
                'status' => 'approved',
                'description' => $description,
            ]);

            $this->loggerService->logDeposit(
                $transaction,
                auth()->user(),
                $oldBalance,
                $account->fresh()->balance
            );

            $this->eventService->dispatchTransactionCreated($transaction);

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
