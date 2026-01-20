<?php

declare(strict_types=1);

namespace App\Domain\Accounts\Composite;

use App\Models\Account;

/**
 * Component interface for Composite Pattern
 * Allows treating individual accounts and account groups uniformly
 */
interface AccountComponent
{
    /**
     * Get the total balance of this component (account or group)
     */
    public function getTotalBalance(): float;

    /**
     * Get the account model if this is a leaf, null if composite
     */
    public function getAccount(): ?Account;

    /**
     * Get all child accounts if this is a composite
     *
     * @return AccountComponent[]
     */
    public function getChildren(): array;

    /**
     * Check if this component can perform a transaction
     */
    public function canPerformTransaction(float $amount): bool;

    /**
     * Get the total daily transaction limit for this component
     */
    public function getDailyLimit(): float;
}
