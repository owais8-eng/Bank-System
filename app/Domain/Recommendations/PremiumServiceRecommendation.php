<?php

declare(strict_types=1);

namespace App\Domain\Recommendations;

use App\Models\User;

class PremiumServiceRecommendation implements RecommendationStrategy
{
    public function recommend(User $user): ?string
    {
        $largeTransactions = $user->transactions()
            ->where('amount', '>', 5000)
            ->count();

        if ($largeTransactions >= 3) {
            return 'You are eligible to subscribe to premium banking services';
        }

        return null;
    }
}
