<?php

namespace App\Http\Controllers\Api;

use App\Models\NotifiableEvent;
use App\Models\NotificationChannel;
use App\Models\ThreadParticipant;
use App\Services\GetNotificationsParamsByProps;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use function config;

class NotificationController extends Controller
{
    private GetNotificationsParamsByProps $notificationsParams;

    public function __construct(GetNotificationsParamsByProps $notificationsParams)
    {
        $this->notificationsParams = $notificationsParams;
    }

    /**
     * @return JsonResponse
     */
    public function getNotificationSettings(): JsonResponse
    {
        return response()->json(
            $this->notificationsParams->getNotificationSettings($this->user())
        );
    }

    /**
     * @return JsonResponse
     */
    public function getBusinessNotificationSettings(): JsonResponse
    {
        return response()->json(
            $this->notificationsParams->getNotificationSettings($this->user())
        );
    }

    /**
     * @return JsonResponse
     */
    public function getNotifications(): JsonResponse
    {
        return response()->json([
            'messageNotificationCount' => ThreadParticipant::where('participant_id', $this->user()->id)
                ->where('last_read', null)
                ->count()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateNotificationSettings(Request $request): JsonResponse
    {
        $user = $this->user();

        $notifiableEvents = $user->isBusiness() ? array_keys(config('constants.businessNotifiableEvents')) : array_keys(config('constants.notifiableEvents'));

        $enityTypeIdField = 'user_id';
        $userId = $user->id;
        $entityEvents = $user->notifiableEvents()->get()->toArray();

        $notifiableEventsPayload = [];
        foreach ($notifiableEvents as $key => $evt) {
            $userEvent = array_filter($entityEvents, function ($item) use ($evt, $enityTypeIdField, $userId) {
                return $item['event'] === $evt && $item[$enityTypeIdField] === $userId;
            });

            $notifiableEventsPayload[] = [
                'id'              => isset($userEvent[$key]) ? $userEvent[$key]['id'] : null,
                $enityTypeIdField => $userId,
                'event'           => $evt,
                'is_enabled'      => ($request->{$evt} === 'false') || ($request->{$evt} === false) ? 0 : 1
            ];
        }

        $notifiableEvents = NotifiableEvent::upsert(
            $notifiableEventsPayload,
            ['id', 'is_enabled']
        );

        $notificationChannelTypesPayload = [];
        $notificationChannels = $this->user()->notificationChannels()->get()->toArray();
        foreach (array_keys(config('constants.notificationChannelTypes')) as $key => $channel) {
            $notificationChannelType = array_filter($notificationChannels, static function ($item) use ($channel, $enityTypeIdField, $userId) {
                return $item['channel'] === $channel && $item[$enityTypeIdField] === $userId;
            });

            $notificationChannelTypesPayload[] = [
                'id'              => isset($notificationChannelType[$key]) ? $notificationChannelType[$key]['id'] : null,
                $enityTypeIdField => $userId,
                'channel'         => $channel,
                'is_enabled'      => (bool)$request->{$channel},
            ];
        }
        $notificationChannelTypes = NotificationChannel::upsert(
            $notificationChannelTypesPayload,
            ['id', 'is_enabled']
        );

        return response()->json(compact('notifiableEvents', 'notificationChannelTypes'));
    }
}
