<?php

declare(strict_types=1);

namespace App\Domain\Notifications;

/**
 * Observer Pattern: Notification Manager
 * Manages multiple notification channels and notifies all observers
 */
class NotificationManager
{
    /**
     * @var NotificationChannel[]
     */
    private array $channels = [];

    /**
     * Register a notification channel
     */
    public function registerChannel(NotificationChannel $channel): void
    {
        $this->channels[$channel->getName()] = $channel;
    }

    /**
     * Remove a notification channel
     */
    public function unregisterChannel(string $channelName): void
    {
        unset($this->channels[$channelName]);
    }

    /**
     * Notify all registered channels
     *
     * @param  string  $message  The notification message
     * @param  array  $data  Additional data for the notification
     * @param  string[]|null  $channels  Specific channels to notify, null for all
     * @return array<string, bool> Results for each channel
     */
    public function notify(string $message, array $data = [], ?array $channels = null): array
    {
        $results = [];
        $channelsToNotify = $channels ?? array_keys($this->channels);

        foreach ($channelsToNotify as $channelName) {
            if (isset($this->channels[$channelName])) {
                $results[$channelName] = $this->channels[$channelName]->send($message, $data);
            }
        }

        return $results;
    }

    /**
     * Get all registered channels
     *
     * @return NotificationChannel[]
     */
    public function getChannels(): array
    {
        return $this->channels;
    }
}
