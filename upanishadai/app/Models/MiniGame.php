<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'config',
        'xp_reward',
    ];

    protected $casts = [
        'config' => 'array',
    ];
}