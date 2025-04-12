class BaseAgent:
    def __init__(self, name, role, tone):
        self.name = name
        self.role = role
        self.tone = tone
    
    def generate_welcome_message(self, context):
        """Generate a welcome message based on context"""
        raise NotImplementedError("Subclasses must implement generate_welcome_message")
    
    def generate_response(self, message, analysis, memory):
        """Generate a response to the user message"""
        raise NotImplementedError("Subclasses must implement generate_response")
    
    def handle_feedback(self, message, response, analysis, memory):
        """Generate feedback and gamification actions"""
        raise NotImplementedError("Subclasses must implement handle_feedback")