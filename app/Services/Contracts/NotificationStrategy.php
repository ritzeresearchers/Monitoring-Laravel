<?php

namespace App\Services\Contracts;

interface NotificationStrategy
{
    public function canNotify(NotifiableEntity $entity, array $settings): bool;
    public function notify($recipient, $data): void;
}
