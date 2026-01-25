<?php

declare(strict_types=1);

namespace App\Domain\Accounts\Composite;

use App\Models\Account;

/**
 * Composite component representing a group of accounts (e.g., family accounts, business accounts)
 */
class AccountGroup implements AccountComponent
{
    /**
     * @var AccountComponent[]
     */
    private array $children = [];

    public function __construct(
        private ?Account $parentAccount = null
    ) {}

    /**
     * Add a child account component to this group
     */
    public function addChild(AccountComponent $component): void
    {
        $this->children[] = $component;
    }

    /**
     * Remove a child account component from this group
     */
    public function removeChild(AccountComponent $component): void
    {
        $this->children = array_filter(
            $this->children,
            fn ($child) => $child !== $component
        );
    }

    public function getTotalBalance(): float
    {
        $total = 0.0;

        foreach ($this->children as $child) {
            $total += $child->getTotalBalance();
        }

        // Include parent account balance if exists
        if ($this->parentAccount !== null) {
            $total += (float) $this->parentAccount->balance;
        }

        return $total;
    }

    public function getAccount(): ?Account
    {
        return $this->parentAccount;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function canPerformTransaction(float $amount): bool
    {
        // Check if any child can perform the transaction
        foreach ($this->children as $child) {
            if ($child->canPerformTransaction($amount)) {
                return true;
            }
        }

        // Check parent account if exists
        if ($this->parentAccount !== null && $this->parentAccount->state === 'active') {
            return true;
        }

        return false;
    }

    public function getDailyLimit(): float
    {
        $totalLimit = 0.0;

        foreach ($this->children as $child) {
            $totalLimit += $child->getDailyLimit();
        }

        if ($this->parentAccount !== null && $this->parentAccount->daily_limit !== null) {
            $totalLimit += (float) $this->parentAccount->daily_limit;
        }

        return $totalLimit;
    }

    /**
     * Get all accounts in this group (flattened)
     *
     * @return Account[]
     */
    public function getAllAccounts(): array
    {
        $accounts = [];

        if ($this->parentAccount !== null) {
            $accounts[] = $this->parentAccount;
        }

        foreach ($this->children as $child) {
            if ($child->getAccount() !== null) {
                $accounts[] = $child->getAccount();
            } elseif ($child instanceof AccountGroup) {
                $accounts = array_merge($accounts, $child->getAllAccounts());
            }
        }

        return $accounts;
    }
}
