<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->info(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('analytics:warehouse:build --day='.now()->toDateString())
    ->dailyAt('01:00');
