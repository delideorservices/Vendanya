<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeworkController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\GamificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth routes (using Laravel Sanctum)
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->middleware('auth:sanctum');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Homework routes
    Route::apiResource('homework', HomeworkController::class);
    
    // Chat routes
    Route::get('/chat/sessions', [ChatController::class, 'getSessions']);
    Route::post('/chat/sessions', [ChatController::class, 'createSession']);
    Route::get('/chat/sessions/{sessionId}', [ChatController::class, 'index']);
    Route::post('/chat/sessions/{sessionId}/messages', [ChatController::class, 'sendMessage']);
    Route::get('/chat/sessions/{sessionId}/messages', [ChatController::class, 'getMessages']);
    Route::put('/chat/sessions/{sessionId}/end', [ChatController::class, 'endSession']);
    
    // Agent routes
    Route::get('/agents', [AgentController::class, 'index']);
    Route::get('/agents/{id}', [AgentController::class, 'show']);
    Route::get('/agents/random', [AgentController::class, 'getRandomAgent']);
    
    // Gamification routes
    Route::get('/gamification/stats', [GamificationController::class, 'getUserStats']);
    Route::get('/gamification/leaderboard', [GamificationController::class, 'getLeaderboard']);
    Route::get('/gamification/mini-games', [GamificationController::class, 'getMiniGames']);
    Route::get('/gamification/mini-games/{id}', [GamificationController::class, 'getMiniGame']);
    Route::post('/gamification/xp', [GamificationController::class, 'earnXP']);
    Route::post('/gamification/badges', [GamificationController::class, 'awardBadge']);
    Route::post('/gamification/streak', [GamificationController::class, 'updateStreak']);
    Route::post('/gamification/retry-token/use', [GamificationController::class, 'useRetryToken']);
    Route::post('/gamification/retry-token/award', [GamificationController::class, 'awardRetryToken']);
    Route::post('/gamification/games/{id}/complete', [GamificationController::class, 'completeGame']);
});

// Public routes for testing/demo purposes only
Route::get('/demo/agents', [AgentController::class, 'index']);