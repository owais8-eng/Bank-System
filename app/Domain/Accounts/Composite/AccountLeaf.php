<?php

declare(strict_types=1);

namespace App\Domain\Accounts\Composite;

use App\Models\Account;

/**
 * Leaf component representing a single account
 */
class AccountLeaf implements AccountComponent
{
    public function __construct(
        private Account $account
    ) {
    }

    public function getTotalBalance(): float
    {
        return (float) $this->account->balance;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function getChildren(): array
    {
        return []; // Leaf has no children
    }

    public function canPerformTransaction(float $amount): bool
    {
        if ($this->account->state !== 'active') {
            return false;
        }

        // Check daily limit if set
        if ($this->account->daily_limit !== null) {
            $dailyTransactions = \App\Models\Transaction::where('account_id', $this->account->id)
                ->whereDate('created_at', today())
                ->sum('amount');

            if (($dailyTransactions + $amount) > $this->account->daily_limit) {
                return false;
            }
        }

        return true;
    }

    public function getDailyLimit(): float
    {
        return (float) ($this->account->daily_limit ?? PHP_FLOAT_MAX);
    }
}
