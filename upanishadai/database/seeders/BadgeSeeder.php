<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run()
    {
        $badges = [
            [
                'name' => 'First Question',
                'description' => 'Asked your first homework question',
                'icon' => 'first_question.png',
                'required_level' => 1,
                'requirements' => json_encode(['questions' => 1]),
            ],
            [
                'name' => 'Rising Scholar',
                'description' => 'Reached level 5',
                'icon' => 'rising_scholar.png',
                'required_level' => 5,
                'requirements' => json_encode(['level' => 5]),
            ],
            [
                'name' => 'Dedicated Learner',
                'description' => 'Maintained a 10-day streak',
                'icon' => 'dedicated_learner.png',
                'required_level' => 1,
                'requirements' => json_encode(['streak' => 10]),
            ],
            [
                'name' => 'Quiz Master',
                'description' => 'Completed 10 quizzes with perfect scores',
                'icon' => 'quiz_master.png',
                'required_level' => 3,
                'requirements' => json_encode(['perfect_quizzes' => 10]),
            ],
            [
                'name' => 'Subject Expert',
                'description' => 'Mastered a specific subject area',
                'icon' => 'subject_expert.png',
                'required_level' => 7,
                'requirements' => json_encode(['subject_mastery' => 1]),
            ],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(
                ['name' => $badge['name']],
                $badge
            );
        }
    }
}
