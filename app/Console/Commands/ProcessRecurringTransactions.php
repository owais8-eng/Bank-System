<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TransactionService;

class ProcessRecurringTransactions extends Command
{
    protected $signature = 'recurring:process';
    protected $description = 'Process recurring transactions';

    public function handle(TransactionService $service)
    {
        $recurrings = \App\Models\RecurringTransaction::dueToday()->get();

        foreach ($recurrings as $recurring) {
            $service->transfer(
                $recurring->fromAccount,
                $recurring->toAccount,
                $recurring->amount,
                'Recurring transaction'
            );

            $recurring->markAsProcessed();
        }
        return Command::SUCCESS;
    }
}
