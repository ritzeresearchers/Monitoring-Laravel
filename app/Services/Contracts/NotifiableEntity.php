<?php

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface NotifiableEntity
{
    public function notificationChannels(): HasMany;
    public function notifiableEvents(): HasMany;
}
