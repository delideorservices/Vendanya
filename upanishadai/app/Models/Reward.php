<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'xp_value',
        'icon',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_rewards')
            ->withPivot('quantity', 'earned_at')
            ->withTimestamps();
    }
}