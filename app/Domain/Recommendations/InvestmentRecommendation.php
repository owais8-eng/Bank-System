<?php

namespace App\Domain\Recommendations;

use App\Domain\Recommendations\RecommendationStrategy;
use App\Models\User;

class InvestmentRecommendation implements RecommendationStrategy
{

    public function recommend(User $user): ?string
    {
        $averageBalance = $user->accounts()->avg('balance');

        if ($averageBalance > 10000) {
            return 'We suggest that you open an investment account with a high annual return.';
        }

        return null;
    }
}
