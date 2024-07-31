<?php

namespace App\Services;

use App\Models\NotifiableEvent;
use App\Models\NotificationChannel;

class NotificationService
{
    /**
     * @param int $userId
     * @param string $event
     * @return bool
     */
    public static function isEventNotifiable(int $userId, string $event): bool
    {
        $notifiableEvent = NotifiableEvent::where('user_id', $userId)
            ->where('event', $event)
            ->first();
        if (!$notifiableEvent) {
            return true;
        }

        return $notifiableEvent->is_enabled == 1;
    }

    /**
     * @param int $userId
     * @param string $channel
     * @return bool
     */
    public static function isChannelEnabled(int $userId, string $channel): bool
    {
        $notificationChannel = NotificationChannel::where('user_id', $userId)
            ->where('channel', $channel)
            ->first();
        if (!$notificationChannel) {
            return true;
        }

        return $notificationChannel->is_enabled == 1;
    }
}
