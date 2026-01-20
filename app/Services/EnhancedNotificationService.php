<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Notifications\Channels\EmailNotificationChannel;
use App\Domain\Notifications\Channels\InAppNotificationChannel;
use App\Domain\Notifications\Channels\SmsNotificationChannel;
use App\Domain\Notifications\NotificationManager;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;

/**
 * Enhanced Notification Service using Observer Pattern
 * Manages multiple notification channels for account activities
 */
class EnhancedNotificationService
{
    private NotificationManager $notificationManager;

    public function __construct()
    {
        $this->notificationManager = new NotificationManager();
        $this->initializeChannels();
    }

    /**
     * Initialize all notification channels
     */
    private function initializeChannels(): void
    {
        $this->notificationManager->registerChannel(new EmailNotificationChannel());
        $this->notificationManager->registerChannel(new SmsNotificationChannel());
        $this->notificationManager->registerChannel(new InAppNotificationChannel());
    }

    /**
     * Notify about account balance change
     */
    public function notifyBalanceChange(Account $account, float $oldBalance, float $newBalance): void
    {
        $user = $account->owner;
        if (! $user) {
            return;
        }

        $message = sprintf(
            'Your %s account balance changed from $%.2f to $%.2f',
            $account->type,
            $oldBalance,
            $newBalance
        );

        $this->notificationManager->notify($message, [
            'user_id' => $user->id,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'account_id' => $account->id,
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
            'subject' => 'Account Balance Update',
        ]);
    }

    /**
     * Notify about large transaction
     */
    public function notifyLargeTransaction(Transaction $transaction, float $threshold = 10000): void
    {
        if ($transaction->amount < $threshold) {
            return;
        }

        $user = $transaction->user;
        if (! $user) {
            return;
        }

        $message = sprintf(
            'Large transaction detected: $%.2f %s',
            $transaction->amount,
            $transaction->type
        );

        $this->notificationManager->notify($message, [
            'user_id' => $user->id,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'transaction_id' => $transaction->id,
            'amount' => $transaction->amount,
            'subject' => 'Large Transaction Alert',
        ]);
    }

    /**
     * Notify about transaction completion
     */
    public function notifyTransactionCompleted(Transaction $transaction): void
    {
        $user = $transaction->user;
        if (! $user) {
            return;
        }

        $message = sprintf(
            'Your %s transaction of $%.2f has been %s',
            $transaction->type,
            $transaction->amount,
            $transaction->status
        );

        $this->notificationManager->notify($message, [
            'user_id' => $user->id,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'transaction_id' => $transaction->id,
            'subject' => 'Transaction Update',
        ]);
    }

    /**
     * Notify about account state change
     */
    public function notifyAccountStateChange(Account $account, string $oldState, string $newState): void
    {
        $user = $account->owner;
        if (! $user) {
            return;
        }

        $message = sprintf(
            'Your account state changed from %s to %s',
            $oldState,
            $newState
        );

        $this->notificationManager->notify($message, [
            'user_id' => $user->id,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'account_id' => $account->id,
            'old_state' => $oldState,
            'new_state' => $newState,
            'subject' => 'Account State Change',
        ]);
    }

    /**
     * Get the notification manager instance
     */
    public function getNotificationManager(): NotificationManager
    {
        return $this->notificationManager;
    }
}
