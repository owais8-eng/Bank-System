<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Channels;

use App\Domain\Notifications\NotificationChannel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * In-app notification channel implementation
 */
class InAppNotificationChannel implements NotificationChannel
{
    public function send(string $message, array $data = []): bool
    {
        try {
            $userId = $data['user_id'] ?? null;
            if (! $userId) {
                return false;
            }

            $user = User::find($userId);
            if (! $user) {
                return false;
            }

            // Use Laravel's notification system
            $user->notify(new \App\Notifications\SimpleNotification($message, $data));

            return true;
        } catch (\Exception $e) {
            Log::error("In-app notification failed: {$e->getMessage()}");

            return false;
        }
    }

    public function getName(): string
    {
        return 'in_app';
    }
}
