<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Notifications\TransactionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTransactionNotification
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

        $user->notify(
            new TransactionNotification($event->transaction)
        );
    }
}
