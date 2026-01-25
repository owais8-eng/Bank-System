<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class TransactionFacade
{
    public function __construct(
        private DepositService $depositService,
        private WithdrawalService $withdrawalService,
        private TransferService $transferService
    ) {}

    public function processDeposit(Account $account, float $amount, ?string $description = null): Transaction
    {
        return $this->depositService->deposit($account, $amount, $description);
    }

    public function processWithdrawal(Account $account, float $amount, ?string $description = null): Transaction
    {
        return $this->withdrawalService->withdraw($account, $amount, $description);
    }

    public function processTransfer(
        Account $fromAccount,
        Account $toAccount,
        float $amount,
        ?string $description = null
    ): Transaction {
        return $this->transferService->transfer($fromAccount, $toAccount, $amount, $description);
    }

    public function processBatch(array $transactions): array
    {
        $results = [];

        foreach ($transactions as $transactionData) {
            try {
                $result = match ($transactionData['type']) {
                    'deposit' => $this->processDeposit(
                        $transactionData['account'],
                        $transactionData['amount'],
                        $transactionData['description'] ?? null
                    ),
                    'withdrawal' => $this->processWithdrawal(
                        $transactionData['account'],
                        $transactionData['amount'],
                        $transactionData['description'] ?? null
                    ),
                    'transfer' => $this->processTransfer(
                        $transactionData['account'],
                        $transactionData['to_account'],
                        $transactionData['amount'],
                        $transactionData['description'] ?? null
                    ),
                    default => throw new \InvalidArgumentException("Unknown transaction type: {$transactionData['type']}"),
                };

                $results[] = $result;
            } catch (\Exception $e) {
                Log::error("Batch transaction failed: {$e->getMessage()}");
                $results[] = null;
            }
        }

        return $results;
    }

    /**
     * Get transaction summary for an account
     * Provides simplified access to transaction information
     */
    public function getAccountSummary(Account $account): array
    {
        $transactions = Transaction::where('account_id', $account->id)
            ->orWhere('to_account_id', $account->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'account_id' => $account->id,
            'balance' => $account->balance,
            'recent_transactions' => $transactions,
            'total_transactions' => Transaction::where('account_id', $account->id)
                ->orWhere('to_account_id', $account->id)
                ->count(),
        ];
    }
}
