<?php

namespace App\Services;

use App\Models\User;
use App\Domain\Recommendations\RecommendationStrategy;

class RecommendationEngine
{
    protected array $strategies;

    public function __construct(array $strategies)
    {
        $this->strategies = $strategies;
    }

    public function generate(User $user): array
    {
        $recommendations = [];

        foreach ($this->strategies as $strategy) {
            $result = $strategy->recommend($user);
            if ($result) {
                $recommendations[] = $result;
            }
        }

        return $recommendations;
    }
}
