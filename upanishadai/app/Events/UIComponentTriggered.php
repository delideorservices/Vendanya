<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UIComponentTriggered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sessionId;
    public $component;
    public $config;

    public function __construct($sessionId, $component, $config = [])
    {
        $this->sessionId = $sessionId;
        $this->component = $component;
        $this->config = $config;
    }

    public function broadcastOn()
    {
        return new Channel('chat.' . $this->sessionId);
    }
}