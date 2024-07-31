<?php

namespace App\Services;

use App\Services\Contracts\NotificationStrategy;
use Illuminate\Support\Facades\Log;

class NotificationHandler
{
    public function executeStrategy(NotificationStrategy $strategy, $recipient, array $data): void
    {
        try {
            $user = $recipient->user ?? $recipient;

            $settings = $this->getNotificationSettings($user);

            if ($strategy->canNotify($recipient, $settings)) {
                $strategy->notify($recipient, $data);
            }
        } catch (\Throwable $throwable) {
            Log::critical('Notification failed', [
                'message' => $throwable->getMessage(),
                'strategyFailed' => $strategy,
            ]);
        }
    }

    protected function getNotificationSettings($user): array
    {
        return (new GetNotificationsParamsByProps())->getNotificationSettings($user);
    }
}
