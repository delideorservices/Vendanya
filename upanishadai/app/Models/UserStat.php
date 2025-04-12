<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'level',
        'xp',
        'questions_answered',
        'games_played',
        'subject_progress',
    ];

    protected $casts = [
        'subject_progress' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}