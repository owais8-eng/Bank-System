<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Notifications\LargeTransactionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLargeTransactionAlert
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
        if ($event->transaction->amount >= 1000) {
            $event->transaction->user->notify(
                new LargeTransactionNotification($event->transaction)
            );
        }
    }
}
