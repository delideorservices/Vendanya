<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Streak extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_streak',
        'max_streak',
        'last_activity_date',
        'retry_tokens',
    ];

    protected $casts = [
        'last_activity_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}