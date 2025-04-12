<?php

namespace App\Events;

use App\Models\User;
use App\Models\Badge;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BadgeEarned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $badge;

    public function __construct(User $user, Badge $badge)
    {
        $this->user = $user;
        $this->badge = $badge;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user->id);
    }

    public function broadcastWith()
    {
        return [
            'badge_id' => $this->badge->id,
            'badge_name' => $this->badge->name,
            'badge_description' => $this->badge->description,
            'badge_icon' => $this->badge->icon,
            'timestamp' => now(),
        ];
    }
}