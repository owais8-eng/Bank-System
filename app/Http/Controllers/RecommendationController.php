<?php

namespace App\Http\Controllers;

use App\Domain\Recommendations\InvestmentRecommendation;
use App\Domain\Recommendations\PremiumServiceRecommendation;
use App\Domain\Recommendations\SavingsRecommendation;
use App\Services\RecommendationEngine;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function recommendations()
    {
        $engine = new RecommendationEngine([
            new InvestmentRecommendation(),
            new SavingsRecommendation(),
            new PremiumServiceRecommendation(),
        ]);

        return response()->json([
            'recommendations' => $engine->generate(auth()->user())
        ]);
    }

}
