<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\RecurringTransaction;
use App\Services\TransactionService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ProcessRecurringTransactions extends Command
{
    protected $signature = 'transactions:run-scheduled';

    protected $description = 'Execute scheduled/recurring transactions';

    public function handle(TransactionService $service)
    {
        $today = Carbon::today();

        $scheduled = RecurringTransaction::where('next_run_at', '<=', $today)->get();

        foreach ($scheduled as $sched) {
            $service->transfer(
                $sched->fromAccount,
                $sched->toAccount,
                $sched->amount,
            );

            switch ($sched->frequency) {
                case 'daily':
                    $sched->next_run = $sched->next_run_at->addDay();
                    break;
                case 'weekly':
                    $sched->next_run = $sched->next_run_at->addWeek();
                    break;
                case 'monthly':
                    $sched->next_run = $sched->next_run_at->addMonth();
                    break;
                case 'yearly':
                    $sched->next_run = $sched->next_run_at->addYear();
                    break;
            }

            $sched->save();
        }

        $this->info('Scheduled transactions executed successfully.');
    }
}
