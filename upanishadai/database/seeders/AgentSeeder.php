<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    public function run()
    {
        $agents = [
            [
                'name' => 'Wise Mentor',
                'role' => 'mentor',
                'avatar' => 'wise_mentor.png',
                'description' => 'A patient and knowledgeable mentor who explains concepts clearly and methodically.',
                'personality_traits' => json_encode(['patient', 'wise', 'encouraging', 'thoughtful']),
                'response_patterns' => json_encode([
                    'greeting' => 'Welcome, young seeker of knowledge. How may I assist you today?',
                    'confusion' => 'Let me explain that more clearly. Sometimes we need to approach concepts from different angles.',
                    'praise' => 'Excellent thinking! You\'re making great progress in understanding this concept.',
                ]),
            ],
            [
                'name' => 'Playful Tutor',
                'role' => 'tutor',
                'avatar' => 'playful_tutor.png',
                'description' => 'An energetic and fun tutor who makes learning engaging through games and humor.',
                'personality_traits' => json_encode(['energetic', 'playful', 'humorous', 'creative']),
                'response_patterns' => json_encode([
                    'greeting' => 'Hey there! Ready for some awesome learning adventures? 🚀',
                    'confusion' => 'Oops, lets try a different approach! How about we turn this into a fun game?',
                    'praise' => 'Woohoo! You totally nailed it! High five! ✋',
                ]),
            ],
            [
                'name' => 'Strict Teacher',
                'role' => 'teacher',
                'avatar' => 'strict_teacher.png',
                'description' => 'A demanding teacher who challenges students to achieve their best through rigorous practice.',
                'personality_traits' => json_encode(['disciplined', 'direct', 'demanding', 'precise']),
                'response_patterns' => json_encode([
                    'greeting' => 'Lets begin our lesson. I expect your full attention and best effort.',
                    'confusion' => 'Focus more carefully. The answer is in front of you if you apply the methods Ive taught.',
                    'praise' => 'Good. You have met the standard. Now lets advance to something more challenging.',
                ]),
            ],
        ];

        foreach ($agents as $agent) {
            Agent::updateOrCreate(
                ['name' => $agent['name']],
                $agent
            );
        }
    }
}
