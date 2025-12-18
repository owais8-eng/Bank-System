<?php

namespace App\Domain\Recommendations;

use App\Domain\Recommendations\RecommendationStrategy;
use App\Models\User;

class SavingsRecommendation implements RecommendationStrategy
{

    public function recommend(User $user): ?string
    {
        $transactions = $user->transactions()->count();

        if ($transactions < 5) {
            return 'A savings account is suitable for you to increase your savings.';
        }

        return null;

    }
}
