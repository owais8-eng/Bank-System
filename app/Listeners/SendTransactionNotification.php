<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Notifications\TransactionNotification;

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
