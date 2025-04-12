<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'homework_id',
        'agent_id',
        'status',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function homework()
    {
        return $this->belongsTo(Homework::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}