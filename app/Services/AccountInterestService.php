<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Strategies\Interest\InterestStrategy\InterestCalculator;
use App\Strategies\Interest\LoanInterestStrategy;
use App\Strategies\Interest\SavingsInterestStrategy as InterestSavingsInterestStrategy;
use InvalidArgumentException;

class AccountInterestService
{
    public function calculateInterest(Account $account): float
    {
        $strategy = match ($account->type) {
            'savings' => new InterestSavingsInterestStrategy,
            'loan' => new LoanInterestStrategy,
            default => throw new InvalidArgumentException('Unsupported account type'),
        };
        $calculator = new InterestCalculator($strategy);

        return $calculator->calculate($account->balance);
    }
}
