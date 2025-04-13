<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserStat;
use App\Models\Streak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create initial user stats
        UserStat::create([
            'user_id' => $user->id,
            'level' => 1,
            'xp' => 0,
            'questions_answered' => 0,
            'games_played' => 0,
        ]);

        // Create streak record
        Streak::create([
            'user_id' => $user->id,
            'current_streak' => 0,
            'max_streak' => 0,
        ]);

        Auth::login($user);

        return response()->json([
            'user' => $user,
            'message' => 'Registration successful',
        ]);
    }
}
