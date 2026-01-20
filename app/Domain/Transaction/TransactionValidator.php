<?php

declare(strict_types=1);

namespace App\Domain\Transaction;

use App\Models\Account;
use DomainException;

class TransactionValidator
{
    public static function validate(Account $from, ?Account $to, float $amount): void
    {
        if ($amount <= 0) {
            throw new DomainException('Amount must be positive');
        }
        if ($from->state !== 'active') {
            throw new DomainException('Source account is not active');
        }
        if ($to->state !== 'active') {
            throw new DomainException('Destination account is not active');
        }

        if ($from->id === $to->id) {
            throw new DomainException('Cannot transfer to same account');
        }

        if ($from->balance < $amount) {
            throw new DomainException('Insufficient balance');
        }
    }
}
