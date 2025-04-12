<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'avatar',
        'description',
        'personality_traits',
        'response_patterns',
    ];

    protected $casts = [
        'personality_traits' => 'array',
        'response_patterns' => 'array',
    ];

    public function chatSessions()
    {
        return $this->hasMany(ChatSession::class);
    }

    public function chatMessages()
    {
        return $this->morphMany(ChatMessage::class, 'sender');
    }
}