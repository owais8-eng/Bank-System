<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Strategies\Interest\InterestStrategy\InterestCalculator;
use App\Strategies\Interest\LoanInterestStrategy;
use App\Strategies\Interest\SavingsInterestStrategy as InterestSavingsInterestStrategy;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class AccountInterestService
{
    public function calculateInterest(Account $account): float
    {
        $cacheKey = "interest_{$account->id}";

        return Cache::remember($cacheKey, 3600, function () use ($account) {
            $strategy = match ($account->type) {
                'savings' => new InterestSavingsInterestStrategy,
                'loan' => new LoanInterestStrategy,
                default => throw new InvalidArgumentException('Unsupported account type'),
            };
            $calculator = new InterestCalculator($strategy);

            return $calculator->calculate($account->balance);
        });
    }

    public function invalidateInterestCache(Account $account): void
    {
        $cacheKey = "interest_{$account->id}";
        Cache::forget($cacheKey);
    }
}
