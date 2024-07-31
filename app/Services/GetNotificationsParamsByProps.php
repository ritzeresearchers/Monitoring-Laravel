<?php

namespace App\Services;

use App\Models\User;

class GetNotificationsParamsByProps
{
    public function getNotificationSettings(User $user): array
    {
        if ($user->user_type === 'business') {
            $entity = $user->business;
            $notifiableEventsConfig = 'constants.businessNotifiableEvents';
        } else {
            $entity = $user;
            $notifiableEventsConfig = 'constants.notifiableEvents';
        }

        $notificationChannels = array_keys(config('constants.notificationChannelTypes'));
        $notifiableEvents = array_keys(config($notifiableEventsConfig));

        $nChannels = array_fill_keys($notificationChannels, false);
        $nEvents = array_fill_keys($notifiableEvents, false);

        foreach ($entity->notificationChannels()->get() as $entityChannel) {
            if (in_array($entityChannel->channel, $notificationChannels, true) && $entityChannel->is_enabled) {
                $nChannels[$entityChannel->channel] = true;
            }
        }

        foreach ($entity->notifiableEvents()->get() as $entityEvent) {
            if (in_array($entityEvent->event, $notifiableEvents, true) && $entityEvent->is_enabled) {
                $nEvents[$entityEvent->event] = true;
            }
        }

        return [
            'notificationChannels' => $nChannels,
            'notifiableEvents'     => $nEvents,
        ];
    }
}
