<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/** A privacy-safe signal that tells open Quattro pages to sync changed data. */
class QuattroDataChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public string $type, public string $revision)
    {
    }

    public function broadcastOn(): array
    {
        return [new Channel('quattro.updates')];
    }

    public function broadcastAs(): string
    {
        return 'quattro.data.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'type' => $this->type,
            'revision' => $this->revision,
            'occurred_at' => now()->toIso8601String(),
        ];
    }
}
