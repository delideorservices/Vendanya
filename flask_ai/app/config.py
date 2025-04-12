import os

# API keys and credentials
OPENAI_API_KEY = os.getenv('OPENAI_API_KEY', 'your-api-key')

# LLM configuration
DEFAULT_LLM_MODEL = "gpt-4o-mini"
ADVANCED_LLM_MODEL = "gpt-4o-mini"

# Agent configuration
AGENT_CONFIGS = {
    'wise_mentor': {
        'name': 'Wise Mentor',
        'role': 'An experienced, patient, and knowledgeable mentor who explains complex concepts clearly',
        'tone': 'calm and encouraging',
    },
    'playful_tutor': {
        'name': 'Playful Tutor',
        'role': 'An enthusiastic and energetic tutor who makes learning fun through games and jokes',
        'tone': 'upbeat and playful',
    },
    'stern_teacher': {
        'name': 'Stern Teacher',
        'role': 'A no-nonsense teacher who focuses on accuracy and expects the best from students',
        'tone': 'direct and challenging',
    },
}

# Game configuration
MINI_GAMES = {
    'quiz': {
        'name': 'Quick Quiz',
        'description': 'Test your knowledge with a quick quiz',
        'xp_reward': 50,
    },
    'memory': {
        'name': 'Memory Match',
        'description': 'Match pairs of cards to test your memory',
        'xp_reward': 30,
    },
    'fill_blanks': {
        'name': 'Fill in the Blanks',
        'description': 'Complete the sentences with the correct words',
        'xp_reward': 40,
    },
}

# UI component configs
UI_COMPONENTS = {
    'quiz': {
        'name': 'Quiz Tab',
        'icon': 'FileText',
    },
    'minigames': {
        'name': 'MiniGames Tab',
        'icon': 'Gamepad',
    },
    'pdf': {
        'name': 'PDF Tab',
        'icon': 'BookOpen',
    },
    'audio': {
        'name': 'Audio Explanation Tab',
        'icon': 'Volume2',
    },
    'story': {
        'name': 'Story Tab',
        'icon': 'Book',
    },
    'joke': {
        'name': 'Joke Tab',
        'icon': 'Laugh',
    },
    'celebrations': {
        'name': 'Celebrations Tab',
        'icon': 'Sparkles',
    },
    'leaderboard': {
        'name': 'Leaderboard Tab',
        'icon': 'Trophy',
    },
}