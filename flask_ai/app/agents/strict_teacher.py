from app.agents.base_agent import BaseAgent
import random

class StrictTeacher(BaseAgent):
    def __init__(self):
        super().__init__(
            name="Strict Teacher",
            role="A no-nonsense teacher who focuses on accuracy and expects the best from students",
            tone="direct and challenging"
        )
    
    def generate_welcome_message(self, context):
        subject = context.get('subject', 'your homework')
        
        welcome_messages = [
            f"Welcome. I expect your full attention as we work through {subject}. What is your question?",
            f"Let's get straight to business. {subject} requires precision and focus. What do you need help with?",
            f"I'm here to ensure you master {subject} completely. What specific aspect are you struggling with?"
        ]
        
        return random.choice(welcome_messages)
    
    def generate_response(self, message, analysis, memory):
        # In a real implementation, this would use an LLM to generate responses
        # For now, return a simple response based on analysis
        
        subject = analysis.get('subject', 'the topic')
        difficulty = analysis.get('difficulty', 'medium')
        intent = analysis.get('intent', 'question')
        
        if intent == 'question':
            return f"Let me be clear about {subject}. The most important principle is... [precise explanation would go here]. Now, can you repeat back what I just explained to ensure you've understood?"
        
        if intent == 'confirm_understanding':
            return f"Correct. Your understanding of {subject} is acceptable. However, to truly master this concept, you should also consider... [advanced context would go here]. Let's move to a more challenging aspect."
        
        if intent == 'express_confusion':
            return f"I see you're having difficulty with {subject}. Pay close attention. [step-by-step explanation would go here]. Now try again, and focus on the key points I've emphasized."
        
        # Default response
        return f"Let's be methodical about {subject}. First, what specific knowledge do you already have on this topic? I need to assess your current understanding."
    
    def handle_feedback(self, message, response, analysis, memory):
        # Calculate accuracy level (0-1)
        accuracy_level = random.uniform(0.5, 1.0)
        
        # Determine XP to award
        xp_amount = int(accuracy_level * 15)  # Strict teacher gives more XP for accuracy
        
        feedback = {
            'accuracy_level': accuracy_level,
            'gamification': [
                {
                    'type': 'xp',
                    'amount': xp_amount,
                    'source': 'accurate_response'
                }
            ]
        }
        
        # Add badge reward for high accuracy
        if accuracy_level > 0.85:
            feedback['gamification'].append({
                'type': 'badge',
                'badge_id': 3,  # Precision badge
            })
        
        # Add streak update
        feedback['gamification'].append({
            'type': 'streak',
        })
        
        # If accuracy is low, no additional rewards
        if accuracy_level < 0.7:
            feedback['gamification'].append({
                'type': 'quiz',
                'config': {
                    'difficulty': 'hard'
                }
            })
        
        return feedback