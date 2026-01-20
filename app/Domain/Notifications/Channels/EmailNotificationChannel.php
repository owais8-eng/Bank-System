<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Channels;

use App\Domain\Notifications\NotificationChannel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Email notification channel implementation
 */
class EmailNotificationChannel implements NotificationChannel
{
    public function send(string $message, array $data = []): bool
    {
        try {
            $email = $data['email'] ?? null;
            if (! $email) {
                return false;
            }

            Mail::raw($message, function ($mail) use ($email, $data) {
                $mail->to($email)
                    ->subject($data['subject'] ?? 'Bank Notification');
            });

            return true;
        } catch (\Exception $e) {
            Log::error("Email notification failed: {$e->getMessage()}");

            return false;
        }
    }

    public function getName(): string
    {
        return 'email';
    }
}
