from app.agents.base_agent import BaseAgent
import random

class PlayfulTutor(BaseAgent):
    def __init__(self):
        super().__init__(
            name="Playful Tutor",
            role="An enthusiastic and energetic tutor who makes learning fun through games and jokes",
            tone="upbeat and playful"
        )
    
    def generate_welcome_message(self, context):
        subject = context.get('subject', 'your homework')
        
        welcome_messages = [
            f"Hey there! 🎮 Ready to make {subject} super fun? Let's dive in!",
            f"Welcome to the fun zone! 🎯 We're going to tackle {subject} together with some games and laughs!",
            f"Hi friend! 🎪 Get ready for a fun adventure through {subject}! What shall we explore first?"
        ]
        
        return random.choice(welcome_messages)
    
    def generate_response(self, message, analysis, memory):
        # In a real implementation, this would use an LLM to generate responses
        # For now, return a simple response based on analysis
        
        subject = analysis.get('subject', 'the topic')
        difficulty = analysis.get('difficulty', 'medium')
        intent = analysis.get('intent', 'question')
        
        if intent == 'question':
            return f"Great question! 🌟 Let's turn this into a game! Imagine {subject} is like... [fun analogy would go here]. Got it? Let's try a quick quiz to make sure it sticks!"
        
        if intent == 'confirm_understanding':
            return f"You got it! 🎉 High five! ✋ That's exactly right about {subject}. Want to level up? Let's try something a bit more challenging!"
        
        if intent == 'express_confusion':
            return f"No worries! Everyone gets stuck sometimes. 🧩 Let's make {subject} easier with a fun memory trick... [playful explanation would go here]. Does that make it click?"
        
        # Default response
        return f"Oh, that's interesting! 🎯 Let's explore {subject} with a quick game! Ready to play and learn at the same time?"
    
    def handle_feedback(self, message, response, analysis, memory):
        # Calculate engagement level (0-1)
        engagement_level = random.uniform(0.7, 1.0)
        
        # Determine XP to award
        xp_amount = int(engagement_level * 10) + 5
        
        feedback = {
            'engagement_level': engagement_level,
            'gamification': [
                {
                    'type': 'xp',
                    'amount': xp_amount,
                    'source': 'engaging_conversation'
                }
            ]
        }
        
        # Add game trigger if appropriate
        if random.random() > 0.5:
            feedback['gamification'].append({
                'type': 'game',
                'game_id': random.randint(1, 3),
                'config': {}
            })
        
        # Add retry token if struggling
        if analysis.get('difficulty', 'medium') == 'hard' and random.random() > 0.7:
            feedback['gamification'].append({
                'type': 'retry_token',
                'amount': 1
            })
        
        # Add streak update
        feedback['gamification'].append({
            'type': 'streak',
        })
        
        return feedback