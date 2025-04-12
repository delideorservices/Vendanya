from app.agents.base_agent import BaseAgent
import random

class WiseMentor(BaseAgent):
    def __init__(self):
        super().__init__(
            name="Wise Mentor",
            role="An experienced, patient, and knowledgeable mentor who explains complex concepts clearly",
            tone="calm and encouraging"
        )
    
    def generate_welcome_message(self, context):
        subject = context.get('subject', 'your homework')
        
        welcome_messages = [
            f"Welcome! I'm here to help you understand {subject} better. What specifically would you like to explore?",
            f"Hello there! I'd be delighted to guide you through {subject}. What questions do you have?",
            f"Greetings! I'm your mentor for {subject}. Ask me anything, and we'll work through it together."
        ]
        
        return random.choice(welcome_messages)
    
    def generate_response(self, message, analysis, memory):
        # In a real implementation, this would use an LLM to generate responses
        # For now, return a simple response based on analysis
        
        subject = analysis.get('subject', 'the topic')
        difficulty = analysis.get('difficulty', 'medium')
        intent = analysis.get('intent', 'question')
        
        if intent == 'question':
            return f"That's an excellent question about {subject}. Let me explain this carefully. The key concept here is that... [detailed explanation would go here]. Does that clarify things for you?"
        
        if intent == 'confirm_understanding':
            return f"You're absolutely right about {subject}. I'd like to add that... [additional context would go here]. You're making great progress!"
        
        if intent == 'express_confusion':
            return f"I understand that {subject} can be challenging. Let's break it down step by step. First... [simplified explanation would go here]. Does that make more sense now?"
        
        # Default response
        return f"I appreciate your interest in {subject}. Let's explore this further together. What specific aspect would you like to understand better?"
    
    def handle_feedback(self, message, response, analysis, memory):
        # Calculate understanding level (0-1)
        understanding_level = random.uniform(0.6, 1.0)
        
        # Determine XP to award
        xp_amount = int(understanding_level * 10) + 5
        
        feedback = {
            'understanding_level': understanding_level,
            'gamification': [
                {
                    'type': 'xp',
                    'amount': xp_amount,
                    'source': 'question_answered'
                }
            ]
        }
        
        # Add badge reward if high understanding
        if understanding_level > 0.9:
            feedback['gamification'].append({
                'type': 'badge',
                'badge_id': 1,  # Quick learner badge
            })
        
        # Add streak update
        feedback['gamification'].append({
            'type': 'streak',
        })
        
        return feedback