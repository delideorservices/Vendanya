<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reward;
use App\Models\Badge;
use App\Models\Streak;
use App\Models\UserStat;
use App\Models\MiniGame;
use App\Events\RewardEarned;
use App\Events\BadgeEarned;
use App\Events\LevelUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GamificationController extends Controller
{
    public function getUserStats()
    {
        $user = Auth::user();
        $stats = $user->stats ?? UserStat::create(['user_id' => $user->id]);
        $streak = $user->streak ?? Streak::create(['user_id' => $user->id]);
        $rewards = $user->rewards()->withPivot('quantity', 'earned_at')->get();
        $badges = $user->badges()->withPivot('earned_at')->get();
        
        return response()->json([
            'stats' => $stats,
            'streak' => $streak,
            'rewards' => $rewards,
            'badges' => $badges
        ]);
    }

    public function getLeaderboard()
    {
        $topUsers = UserStat::with('user')
            ->orderBy('level', 'desc')
            ->orderBy('xp', 'desc')
            ->take(10)
            ->get();
        
        return response()->json([
            'leaderboard' => $topUsers
        ]);
    }

    public function getMiniGames()
    {
        $games = MiniGame::all();
        
        return response()->json([
            'mini_games' => $games
        ]);
    }

    public function getMiniGame($id)
    {
        $game = MiniGame::findOrFail($id);
        
        return response()->json([
            'mini_game' => $game
        ]);
    }

    public function earnXP(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
            'source' => 'required|string',
        ]);

        $user = Auth::user();
        $stats = $user->stats ?? UserStat::create(['user_id' => $user->id]);
        
        // Add XP to the user
        $oldLevel = $stats->level;
        $stats->xp += $validated['amount'];
        
        // Calculate new level
        $newLevel = floor(sqrt($stats->xp / 100)) + 1;
        
        if ($newLevel > $oldLevel) {
            $stats->level = $newLevel;
            event(new LevelUp($user, $newLevel));
        }
        
        $stats->save();
        
        return response()->json([
            'message' => 'XP added successfully',
            'stats' => $stats,
            'level_up' => $newLevel > $oldLevel
        ]);
    }

    public function awardBadge(Request $request)
    {
        $validated = $request->validate([
            'badge_id' => 'required|exists:badges,id',
        ]);

        $user = Auth::user();
        $badge = Badge::findOrFail($validated['badge_id']);
        
        // Check if user already has this badge
        if (!$user->badges()->where('badge_id', $badge->id)->exists()) {
            $user->badges()->attach($badge->id, [
                'earned_at' => now(),
            ]);
            
            event(new BadgeEarned($user, $badge));
        }
        
        return response()->json([
            'message' => 'Badge awarded successfully',
            'badge' => $badge
        ]);
    }

    public function updateStreak()
    {
        $user = Auth::user();
        $streak = $user->streak ?? Streak::create(['user_id' => $user->id]);
        
        $lastActivity = $streak->last_activity_date;
        $today = now()->startOfDay();
        
        if (!$lastActivity) {
            // First activity
            $streak->current_streak = 1;
            $streak->max_streak = 1;
        } else if ($lastActivity->diffInDays($today) === 0) {
            // Already updated today
            return response()->json([
                'message' => 'Streak already updated today',
                'streak' => $streak
            ]);
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
        
        return response()->json([
            'message' => 'Streak updated successfully',
            'streak' => $streak
        ]);
    }

    public function completeGame(Request $request)
    {
        $validated = $request->validate([
            'game_id' => 'required|exists:mini_games,id',
            'score' => 'required|integer|min:0',
        ]);

        $user = Auth::user();
        $game = MiniGame::findOrFail($validated['game_id']);
        $stats = $user->stats ?? UserStat::create(['user_id' => $user->id]);
        
        // Update games played count
        $stats->games_played += 1;
        $stats->save();
        
        // Award XP based on game completion and score
        $xpEarned = min($game->xp_reward, $validated['score']);
        $this->earnXP(new Request([
            'amount' => $xpEarned,
            'source' => 'mini_game_' . $game->id,
        ]));
        
        return response()->json([
            'message' => 'Game completed successfully',
            'xp_earned' => $xpEarned
        ]);
    }

    public function useRetryToken(Request $request)
    {
        $user = Auth::user();
        $streak = $user->streak ?? Streak::create(['user_id' => $user->id]);
        
        if ($streak->retry_tokens <= 0) {
            return response()->json([
                'message' => 'No retry tokens available',
                'streak' => $streak
            ], 400);
        }
        
        $streak->retry_tokens -= 1;
        $streak->save();
        
        return response()->json([
            'message' => 'Retry token used successfully',
            'streak' => $streak
        ]);
    }

    public function awardRetryToken(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|integer|min:1|max:5',
        ]);

        $user = Auth::user();
        $streak = $user->streak ?? Streak::create(['user_id' => $user->id]);
        
        $streak->retry_tokens += $validated['amount'];
        $streak->save();
        
        return response()->json([
            'message' => 'Retry tokens awarded successfully',
            'streak' => $streak
        ]);
    }
}