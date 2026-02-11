<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DataChanged implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    // Channel PUBLIC (bukan private)
    public function broadcastOn(): Channel
    {
        return new Channel('data-channel');
    }

    // Nama event di Echo (penting!)
    public function broadcastAs(): string
    {
        return 'DataChanged';
    }
}
