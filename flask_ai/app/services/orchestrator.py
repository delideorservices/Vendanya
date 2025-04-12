from app.agents.wise_mentor import WiseMentor
from app.agents.playful_tutor import PlayfulTutor
from app.agents.strict_teacher import StrictTeacher
from app.tasks.analyze_input import AnalyzeInput
from app.tasks.generate_response import GenerateResponse
from app.tasks.feedback_engine import FeedbackEngine
from app.services.memory_service import MemoryService
import random

class Orchestrator:
    def __init__(self):
        self.agents = {
            1: WiseMentor(),
            2: PlayfulTutor(),
            3: StrictTeacher(),
        }
        self.analyze_task = AnalyzeInput()
        self.response_task = GenerateResponse()
        self.feedback_task = FeedbackEngine()
        self.memory_service = MemoryService()
        
    def initialize_session(self, session_id, user_id, agent_id, context):
        # Get agent based on agent_id or select randomly if not specified
        agent = self.agents.get(agent_id) if agent_id else random.choice(list(self.agents.values()))
        
        # Initialize memory for this session
        self.memory_service.initialize_memory(session_id, context)
        
        # Generate welcome message
        welcome_message = agent.generate_welcome_message(context)
        
        # Determine initial UI components to show
        ui_components = [
            {
                'name': 'quiz',
                'config': {}
            },
            {
                'name': 'minigames',
                'config': {}
            }
        ]
        
        return {
            'agent_id': agent_id,
            'welcome_message': welcome_message,
            'ui_components': ui_components,
        }
    
    def process_message(self, session_id, message_content):
        # Retrieve session memory
        memory = self.memory_service.get_memory(session_id)
        
        # Analyze the input
        analysis_result = self.analyze_task.run(message_content, memory)
        
        # Determine which agent to use based on analysis
        agent_id = memory.get('agent_id', 1)
        agent = self.agents.get(agent_id)
        
        # Generate response
        response = self.response_task.run(message_content, analysis_result, agent, memory)
        
        # Generate feedback and gamification actions
        feedback = self.feedback_task.run(message_content, response, analysis_result, memory)
        
        # Determine UI components to trigger
        ui_components = self._determine_ui_components(analysis_result, feedback)
        
        # Update memory
        self.memory_service.update_memory(session_id, {
            'last_message': message_content,
            'last_response': response,
            'last_analysis': analysis_result,
        })
        
        return {
            'response': response,
            'confidence': analysis_result.get('confidence', 0.8),
            'intent': analysis_result.get('intent', 'inform'),
            'ui_components': ui_components,
            'gamification': feedback.get('gamification', []),
        }
    
    def _determine_ui_components(self, analysis, feedback):
        components = []
        
        subject = analysis.get('subject')
        difficulty = analysis.get('difficulty', 'medium')
        intent = analysis.get('intent', 'inform')
        
        # Add quiz component if appropriate
        if intent in ['question', 'check_understanding']:
            components.append({
                'name': 'quiz',
                'config': {
                    'questions': self._generate_quiz_questions(subject, difficulty),
                }
            })
        
        # Add celebration if user showed good understanding
        if feedback.get('understanding_level', 0) > 0.8:
            components.append({
                'name': 'celebrations',
                'config': {}
            })
        
        # Add other components based on analysis
        if 'need_visualization' in analysis and analysis['need_visualization']:
            components.append({
                'name': 'pdf',
                'config': {}
            })
        
        if 'emotional_state' in analysis and analysis['emotional_state'] == 'frustrated':
            components.append({
                'name': 'joke',
                'config': {}
            })
        
        return components
    
    def _generate_quiz_questions(self, subject, difficulty):
        # In a real implementation, this would generate questions based on the subject
        # For now, return some sample questions
        return [
            {
                'question': 'What is the capital of France?',
                'options': ['Berlin', 'Madrid', 'Paris', 'Rome'],
                'correctIndex': 2
            },
            {
                'question': 'Which planet is known as the Red Planet?',
                'options': ['Venus', 'Mars', 'Jupiter', 'Saturn'],
                'correctIndex': 1
            },
            {
                'question': 'What is 2 + 2?',
                'options': ['3', '4', '5', '22'],
                'correctIndex': 1
            }
        ]