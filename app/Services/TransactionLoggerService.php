<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;

class TransactionLoggerService
{
    public function logDeposit(Transaction $transaction, User $user, float $oldBalance, float $newBalance): void
    {
        ActivityLogger::log(
            'Deposit transaction created',
            $transaction,
            $user,
            [
                'amount' => $transaction->amount,
                'old_balance' => $oldBalance,
                'new_balance' => $newBalance,
            ],
            'transaction'
        );
    }

    public function logWithdrawal(Transaction $transaction, User $user, float $oldBalance, float $newBalance): void
    {
        ActivityLogger::log(
            'Withdrawal transaction created',
            $transaction,
            $user,
            [
                'amount' => $transaction->amount,
                'old_balance' => $oldBalance,
                'new_balance' => $newBalance,
            ],
            'transaction'
        );
    }

    public function logTransfer(Transaction $transaction, User $user, int $fromAccountId, int $toAccountId): void
    {
        ActivityLogger::log(
            'Transfer transaction created',
            $transaction,
            $user,
            [
                'amount' => $transaction->amount,
                'from_account' => $fromAccountId,
                'to_account' => $toAccountId,
            ],
            'transaction'
        );
    }

    public function logAccountBalanceUpdate($account, User $user, float $oldBalance, float $newBalance, string $context = 'account'): void
    {
        ActivityLogger::log(
            ucfirst($context) . ' balance updated',
            $account,
            $user,
            [
                'old_balance' => $oldBalance,
                'new_balance' => $newBalance,
            ],
            'account'
        );
    }
}
