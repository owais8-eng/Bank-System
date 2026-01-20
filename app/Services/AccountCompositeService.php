<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Accounts\Composite\AccountComponent;
use App\Domain\Accounts\Composite\AccountCompositeFactory;
use App\Models\Account;

/**
 * Service for working with account composite structures
 * Demonstrates Composite Pattern usage
 */
class AccountCompositeService
{
    public function __construct(
        private AccountCompositeFactory $compositeFactory
    ) {
    }

    /**
     * Get total balance for an account and all its children
     */
    public function getTotalBalance(Account $account): float
    {
        $component = $this->compositeFactory->buildComposite($account);

        return $component->getTotalBalance();
    }

    /**
     * Check if account or any child account can perform a transaction
     */
    public function canPerformTransaction(Account $account, float $amount): bool
    {
        $component = $this->compositeFactory->buildComposite($account);

        return $component->canPerformTransaction($amount);
    }

    /**
     * Get all accounts in a hierarchy (including children)
     *
     * @return Account[]
     */
    public function getAllAccountsInHierarchy(Account $account): array
    {
        $component = $this->compositeFactory->buildComposite($account);

        if ($component instanceof \App\Domain\Accounts\Composite\AccountGroup) {
            return $component->getAllAccounts();
        }

        return [$component->getAccount()];
    }

    /**
     * Get daily limit for account group
     */
    public function getGroupDailyLimit(Account $account): float
    {
        $component = $this->compositeFactory->buildComposite($account);

        return $component->getDailyLimit();
    }

    /**
     * Build a composite from multiple accounts
     *
     * @param Account[] $accounts
     */
    public function buildGroup(array $accounts, ?Account $parentAccount = null): AccountComponent
    {
        return $this->compositeFactory->buildGroup($accounts, $parentAccount);
    }
}
