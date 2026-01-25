<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Transaction\Roles\RoleResolve;
use App\Domain\Transaction\TransactionValidator;
use App\Events\TransactionCreated;
use App\Models\Account;
use App\Models\Transaction;
use DomainException;
use Illuminate\Support\Facades\DB;

class TransferService
{
    public function __construct(
        private AccountBalanceService $balanceService,
        private TransactionLoggerService $loggerService,
        private TransactionApprovalService $approvalService
    ) {}

    public function transfer(Account $from, Account $to, float $amount, ?string $description = null): Transaction
    {
        TransactionValidator::validate($from, $to, $amount);

        $role = RoleResolve::resolve(auth()->user()->role);

        if (! $role->canApproveTransaction($amount)) {
            throw new DomainException('User not authorized to approve this transaction');
        }

        return DB::transaction(function () use ($from, $to, $amount, $description) {
            $fromOldBalance = $from->balance;
            $toOldBalance = $to->balance;

            $transaction = Transaction::create([
                'account_id' => $from->id,
                'user_id' => auth()->id(),
                'type' => 'transfer',
                'to_account_id' => $to->id,
                'amount' => $amount,
                'status' => 'pending',
                'description' => $description,
                'approved_type' => null,
            ]);

            $this->loggerService->logTransfer(
                $transaction,
                auth()->user(),
                $from->id,
                $to->id
            );

            $this->approvalService->approve($transaction);

            $this->balanceService->decrementBalance($from, $amount);
            $this->balanceService->incrementBalance($to, $amount);

            $this->loggerService->logAccountBalanceUpdate(
                $from,
                auth()->user(),
                $fromOldBalance,
                $from->fresh()->balance,
                'from account'
            );

            $this->loggerService->logAccountBalanceUpdate(
                $to,
                auth()->user(),
                $toOldBalance,
                $to->fresh()->balance,
                'to account'
            );


            event(new TransactionCreated($transaction));

            return $transaction->fresh();
        });
    }
}
