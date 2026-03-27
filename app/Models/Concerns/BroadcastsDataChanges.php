<?php

namespace App\Models\Concerns;

use App\Events\DataChanged;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Support\Facades\Log;

trait BroadcastsDataChanges
{
    protected static function broadcastDataChanged(mixed $payload): void
    {
        try {
            broadcast(new DataChanged($payload));
        } catch (BroadcastException $exception) {
            Log::warning('Skipping realtime broadcast because the broadcaster is unavailable.', [
                'model' => static::class,
                'payload_type' => is_object($payload) ? get_class($payload) : gettype($payload),
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
