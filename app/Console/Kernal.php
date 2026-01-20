<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Support\Facades\Schedule;

class Kernal
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('transactions:run-scheduled')->daily();
    }
}
