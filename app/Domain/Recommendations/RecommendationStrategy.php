<?php

declare(strict_types=1);

namespace App\Domain\Recommendations;

use App\Models\User;

interface RecommendationStrategy
{
    public function recommend(User $user): ?string;
}
