<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class QuoteAccepted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Collection $quotes;

    /**
     * @param Collection $quotes
     */
    public function __construct(Collection $quotes)
    {
        $this->quotes = $quotes;
    }
}
