<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Accounts\Composite\AccountCompositeFactory;
use App\Services\AccountCompositeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register AccountCompositeService with its dependencies
        $this->app->singleton(AccountCompositeService::class, function ($app) {
            return new AccountCompositeService(
                new AccountCompositeFactory()
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
