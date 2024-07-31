<?php

namespace App\Http\Controllers\Api;

use App\Models\BusinessNotifiableEvent;
use App\Models\BusinessNotificationChannel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use function config;

class BusinessNotificationController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateNotificationSettings(Request $request): JsonResponse
    {
        $businessId = $this->user()->business_id;
        $business = $this->user()->business;

        $enityTypeIdField = 'business_id';
        $entityEvents = $business->notifiableEvents()->get()->toArray();
        $notificationChannels = $business->notificationChannels()->get()->toArray();

        $notifiableEventsPayload = [];
        foreach (array_keys(config('constants.businessNotifiableEvents')) as $key => $evt) {
            $userEvent = array_filter($entityEvents, static function ($item) use ($evt, $enityTypeIdField, $businessId) {
                return $item['event'] === $evt && $item[$enityTypeIdField] === $businessId;
            });

            $notifiableEventsPayload[] = [
                'id' => isset($userEvent[$key]) ? $userEvent[$key]['id'] : null,
                $enityTypeIdField => $businessId,
                'event' => $evt,
                'is_enabled' => ($request->{$evt} === 'false') || ($request->{$evt} === false) ? 0 : 1
            ];
        }

        $notifiableEvents = BusinessNotifiableEvent::upsert(
            $notifiableEventsPayload,
            ['id', 'is_enabled']
        );
        $notificationChannelTypesPayload = [];

        foreach (array_keys(config('constants.notificationChannelTypes')) as $key => $channel) {
            $notificationChannelType = array_filter($notificationChannels, static function ($item) use ($channel, $enityTypeIdField, $businessId) {
                return $item['channel'] === $channel && $item[$enityTypeIdField] === $businessId;
            });

            $notificationChannelTypesPayload[] = [
                'id' => isset($notificationChannelType[$key]) ? $notificationChannelType[$key]['id'] : null,
                $enityTypeIdField => $businessId,
                'channel' => $channel,
                'is_enabled' => (bool) $request->{$channel}
            ];
        }

        return response()->json([
            'notifiableEvents' => $notifiableEvents,
            'notificationChannelTypes' => BusinessNotificationChannel::upsert(
                $notificationChannelTypesPayload,
                ['id', 'is_enabled']
            ),
        ]);
    }
}
