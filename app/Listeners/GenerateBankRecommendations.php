<?php

namespace App\Listeners;

use App\Domain\Recommendations\InvestmentRecommendation;
use App\Domain\Recommendations\PremiumServiceRecommendation;
use App\Domain\Recommendations\SavingsRecommendation;
use App\Events\TransactionCreated;
use App\Notifications\RecommendationNotification;
use App\Services\RecommendationEngine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateBankRecommendations
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TransactionCreated $event): void
    {
        $user = $event->transaction->user;

        $engine = new RecommendationEngine([
            new InvestmentRecommendation(),
            new SavingsRecommendation(),
            new PremiumServiceRecommendation(),
        ]);

        $recommendations = $engine->generate($user);

        foreach ($recommendations as $message) {
            $user->notify(
                new RecommendationNotification($message)
            );
        }
    }
}
