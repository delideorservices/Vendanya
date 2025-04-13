<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    public function run()
    {
        $rewards = [
            [
                'name' => 'Quick Learner',
                'type' => 'xp_boost',
                'description' => 'Earn 10% more XP for the next hour',
                'xp_value' => 0,
                'icon' => 'quick_learner.png',
            ],
            [
                'name' => 'Streak Shield',
                'type' => 'streak_protection',
                'description' => 'Protects your streak for one day if you miss',
                'xp_value' => 0,
                'icon' => 'streak_shield.png',
            ],
            [
                'name' => 'Wisdom Crystal',
                'type' => 'hint',
                'description' => 'Get a helpful hint when you\'re stuck',
                'xp_value' => 0,
                'icon' => 'wisdom_crystal.png',
            ],
            [
                'name' => 'Knowledge Surge',
                'type' => 'xp',
                'description' => 'Instant XP reward',
                'xp_value' => 50,
                'icon' => 'knowledge_surge.png',
            ],
            [
                'name' => 'Weekly Streak',
                'type' => 'streak',
                'description' => 'Maintained a 7-day streak',
                'xp_value' => 100,
                'icon' => 'weekly_streak.png',
            ],
        ];

        foreach ($rewards as $reward) {
            Reward::updateOrCreate(
                ['name' => $reward['name']],
                $reward
            );
        }
    }
}
