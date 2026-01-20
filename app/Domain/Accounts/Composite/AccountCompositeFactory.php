<?php

declare(strict_types=1);

namespace App\Domain\Accounts\Composite;

use App\Models\Account;

/**
 * Factory for creating account composite structures
 */
class AccountCompositeFactory
{
    /**
     * Build a composite structure from an account and its children
     */
    public function buildComposite(Account $account): AccountComponent
    {
        $children = $account->children();

        // If account has no children, return a leaf
        if ($children->count() === 0) {
            return new AccountLeaf($account);
        }

        // Build a group with parent account
        $group = new AccountGroup($account);

        // Add all child accounts as components
        foreach ($children->get() as $childAccount) {
            $childComponent = $this->buildComposite($childAccount);
            $group->addChild($childComponent);
        }

        return $group;
    }

    /**
     * Build a group from multiple accounts
     *
     * @param Account[] $accounts
     */
    public function buildGroup(array $accounts, ?Account $parentAccount = null): AccountGroup
    {
        $group = new AccountGroup($parentAccount);

        foreach ($accounts as $account) {
            $component = $this->buildComposite($account);
            $group->addChild($component);
        }

        return $group;
    }
}
