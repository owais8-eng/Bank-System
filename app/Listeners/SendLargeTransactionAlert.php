<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Notifications\LargeTransactionNotification;

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
