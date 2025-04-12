<?php

namespace App\Events;

use App\Models\User;
use App\Models\Reward;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RewardEarned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $reward;

    public function __construct(User $user, Reward $reward)
    {
        $this->user = $user;
        $this->reward = $reward;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user->id);
    }

    public function broadcastWith()
    {
        return [
            'reward_id' => $this->reward->id,
            'reward_name' => $this->reward->name,
            'reward_type' => $this->reward->type,
            'reward_description' => $this->reward->description,
            'reward_icon' => $this->reward->icon,
            'xp_value' => $this->reward->xp_value,
            'timestamp' => now(),
        ];
    }
}