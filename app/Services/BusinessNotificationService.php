<?php

namespace App\Services;

use App\Models\BusinessNotifiableEvent;
use App\Models\BusinessNotificationChannel;

class BusinessNotificationService
{
    /**
     * @param int $businessId
     * @param string $event
     * @return bool
     */
    public static function isEventNotifiable(int $businessId, string $event): bool
    {
        $notifiableEvent = BusinessNotifiableEvent::where('business_id', $businessId)
            ->where('event', $event)
            ->first();
        if (!$notifiableEvent) {
            return true;
        }

        return $notifiableEvent->is_enabled === true;
    }

    /**
     * @param int $businessId
     * @param string $channel
     * @return bool
     */
    public static function isChannelEnabled(int $businessId, string $channel): bool
    {
        $notificationChannel = BusinessNotificationChannel::where('business_id', $businessId)
            ->where('channel', $channel)
            ->first();
        if (!$notificationChannel) {
            return true;
        }

        return $notificationChannel->is_enabled === true;
    }
}
