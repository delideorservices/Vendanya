<?php

namespace Database\Seeders;

use App\Models\MiniGame;
use Illuminate\Database\Seeder;

class MiniGameSeeder extends Seeder
{
    public function run()
    {
        $miniGames = [
            [
                'name' => 'Quick Quiz',
                'type' => 'quiz',
                'description' => 'Test your knowledge with a quick quiz',
                'config' => json_encode([
                    'time_limit' => 60,
                    'questions_per_round' => 5,
                ]),
                'xp_reward' => 50,
            ],
            [
                'name' => 'Memory Match',
                'type' => 'memory',
                'description' => 'Match pairs of cards to test your memory',
                'config' => json_encode([
                    'difficulty' => 'medium',
                    'pairs' => 8,
                ]),
                'xp_reward' => 30,
            ],
            [
                'name' => 'Word Scramble',
                'type' => 'word',
                'description' => 'Unscramble words related to your homework',
                'config' => json_encode([
                    'time_limit' => 90,
                    'words_per_round' => 5,
                ]),
                'xp_reward' => 40,
            ],
        ];

        foreach ($miniGames as $game) {
            MiniGame::updateOrCreate(
                ['name' => $game['name']],
                $game
            );
        }
    }
}
