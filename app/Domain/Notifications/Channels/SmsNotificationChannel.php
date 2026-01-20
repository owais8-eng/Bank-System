<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Channels;

use App\Domain\Notifications\NotificationChannel;
use Illuminate\Support\Facades\Log;

/**
 * SMS notification channel implementation
 */
class SmsNotificationChannel implements NotificationChannel
{
    public function send(string $message, array $data = []): bool
    {
        try {
            $phone = $data['phone'] ?? null;
            if (! $phone) {
                return false;
            }

            // In a real implementation, this would integrate with an SMS gateway
            // For now, we'll log it
            Log::info("SMS sent to {$phone}: {$message}");

            return true;
        } catch (\Exception $e) {
            Log::error("SMS notification failed: {$e->getMessage()}");

            return false;
        }
    }

    public function getName(): string
    {
        return 'sms';
    }
}
