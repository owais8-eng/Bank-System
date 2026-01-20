<?php

declare(strict_types=1);

namespace App\Domain\Notifications;

/**
 * Observer Pattern: Notification Channel Interface
 * Defines the interface for different notification channels
 */
interface NotificationChannel
{
    /**
     * Send a notification through this channel
     *
     * @param string $message The notification message
     * @param array $data Additional data for the notification
     */
    public function send(string $message, array $data = []): bool;

    /**
     * Get the channel name
     */
    public function getName(): string;
}
