<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function homeworks()
    {
        return $this->hasMany(Homework::class);
    }

    public function chatSessions()
    {
        return $this->hasMany(ChatSession::class);
    }

    public function chatMessages()
    {
        return $this->morphMany(ChatMessage::class, 'sender');
    }

    public function rewards()
    {
        return $this->belongsToMany(Reward::class, 'user_rewards')
            ->withPivot('quantity', 'earned_at')
            ->withTimestamps();
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('earned_at')
            ->withTimestamps();
    }

    public function streak()
    {
        return $this->hasOne(Streak::class);
    }

    public function stats()
    {
        return $this->hasOne(UserStat::class);
    }
}