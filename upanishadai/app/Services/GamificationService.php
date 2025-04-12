<?php

namespace App\Services;

use App\Models\User;
use App\Models\Reward;
use App\Models\Badge;
use App\Models\Streak;
use App\Models\UserStat;
use App\Models\MiniGame;
use App\Events\RewardEarned;
use App\Events\BadgeEarned;
use App\Events\LevelUp;
use App\Events\UIComponentTriggered;
use Illuminate\Support\Facades\Log;

class GamificationService
{
    public function awardXP($userId, $amount, $source)
    {
        $user = User::findOrFail($userId);
        $stats = $user->stats ?? UserStat::create(['user_id' => $user->id]);
        
        // Add XP to the user
        $oldLevel = $stats->level;
        $stats->xp += $amount;
        
        // Calculate new level
        $newLevel = floor(sqrt($stats->xp / 100)) + 1;
        
        if ($newLevel > $oldLevel) {
            $stats->level = $newLevel;
            event(new LevelUp($user, $newLevel));
            
            // Check for level-based badges
            $levelBadges = Badge::where('required_level', '<=', $newLevel)
                ->whereDoesntExist(function ($query) use ($userId) {
                    $query->selectRaw(1)
                        ->from('user_badges')
                        ->whereColumn('badge_id', 'badges.id')
                        ->where('user_id', $userId);
                })
                ->get();
            
            foreach ($levelBadges as $badge) {
                $this->awardBadge($userId, $badge->id);
            }
        }
        
        $stats->save();
        
        return $stats;
    }

    public function awardBadge($userId, $badgeId)
    {
        $user = User::findOrFail($userId);
        $badge = Badge::findOrFail($badgeId);
        
        // Check if user already has this badge
        if (!$user->badges()->where('badge_id', $badge->id)->exists()) {
            $user->badges()->attach($badge->id, [
                'earned_at' => now(),
            ]);
            
            event(new BadgeEarned($user, $badge));
            return true;
        }
        
        return false;
    }

    public function updateStreak($userId)
    {
        $user = User::findOrFail($userId);
        $streak = $user->streak ?? Streak::create(['user_id' => $user->id]);
        
        $lastActivity = $streak->last_activity_date;
        $today = now()->startOfDay();
        
        if (!$lastActivity) {
            // First activity
            $streak->current_streak = 1;
            $streak->max_streak = 1;
        } else if ($lastActivity->diffInDays($today) === 0) {
            // Already updated today
            return $streak;
        } else if ($lastActivity->diffInDays($today) === 1) {
            // Consecutive day
            $streak->current_streak += 1;
            
            // Update max streak if needed
            if ($streak->current_streak > $streak->max_streak) {
                $streak->max_streak = $streak->current_streak;
            }
        } else {
            // Streak broken
            $streak->current_streak = 1;
        }
        
        $streak->last_activity_date = $today;
        $streak->save();
        
        // Award streak-based rewards if applicable
        if ($streak->current_streak % 7 === 0) {
            // Weekly streak reward
            $weeklyStreakReward = Reward::where('name', 'Weekly Streak')->first();
            if ($weeklyStreakReward) {
                $user->rewards()->attach($weeklyStreakReward->id, [
                    'quantity' => 1,
                    'earned_at' => now(),
                ]);
                
                event(new RewardEarned($user, $weeklyStreakReward));
            }
        }
        
        return $streak;
    }

    public function awardRetryToken($userId, $amount = 1)
    {
        $user = User::findOrFail($userId);
        $streak = $user->streak ?? Streak::create(['user_id' => $user->id]);
        
        $streak->retry_tokens += $amount;
        $streak->save();
        
        return $streak;
    }

    public function useRetryToken($userId)
    {
        $user = User::findOrFail($userId);
        $streak = $user->streak ?? Streak::create(['user_id' => $user->id]);
        
        if ($streak->retry_tokens <= 0) {
            return false;
        }
        
        $streak->retry_tokens -= 1;
        $streak->save();
        
        return true;
    }

    public function triggerGame($sessionId, $gameId, $config = [])
    {
        $game = MiniGame::findOrFail($gameId);
        
        // Merge game config with provided config
        $finalConfig = array_merge($game->config, $config);
        
        // Trigger the game UI component
        broadcast(new UIComponentTriggered(
            $sessionId,
            'minigame',
            [
                'game_id' => $game->id,
                'game_type' => $game->type,
                'game_name' => $game->name,
                'config' => $finalConfig,
            ]
        ));
        
        return true;
    }

    public function completeGame($userId, $gameId, $score)
    {
        $user = User::findOrFail($userId);
        $game = MiniGame::findOrFail($gameId);
        $stats = $user->stats ?? UserStat::create(['user_id' => $user->id]);
        
        // Update games played count
        $stats->games_played += 1;
        $stats->save();
        
        // Award XP based on game completion and score
        $xpEarned = min($game->xp_reward, $score);
        $this->awardXP($userId, $xpEarned, 'mini_game_' . $game->id);
        
        return $xpEarned;
    }
}