from flask import Blueprint, request, jsonify
from app.services.orchestrator import Orchestrator
from app.services.state_tracker import StateTracker

api_bp = Blueprint('api', __name__)
orchestrator = Orchestrator()
state_tracker = StateTracker()

@api_bp.route('/initialize', methods=['POST'])
def initialize_session():
    data = request.json
    session_id = data.get('session_id')
    user_id = data.get('user_id')
    agent_id = data.get('agent_id')
    context = data.get('context', {})
    
    try:
        # Initialize session state
        state_tracker.initialize_session(session_id, user_id, agent_id, context)
        
        # Get initial response from orchestrator
        result = orchestrator.initialize_session(session_id, user_id, agent_id, context)
        
        return jsonify(result)
    except Exception as e:
        print(f"Error initializing session: {e}")
        return jsonify({'error': str(e)}), 500

@api_bp.route('/process', methods=['POST'])
def process_message():
    data = request.json
    session_id = data.get('session_id')
    message_id = data.get('message_id')
    user_id = data.get('user_id')
    content = data.get('content')
    
    try:
        # Update session state with new message
        state_tracker.add_message(session_id, message_id, 'user', content)
        
        # Process message with orchestrator
        result = orchestrator.process_message(session_id, content)
        
        # Update session state with AI response
        state_tracker.add_message(session_id, None, 'agent', result['response'])
        
        return jsonify(result)
    except Exception as e:
        print(f"Error processing message: {e}")
        return jsonify({'error': str(e)}), 500

@api_bp.route('/state/<session_id>', methods=['GET'])
def get_session_state(session_id):
    try:
        state = state_tracker.get_session_state(session_id)
        return jsonify(state)
    except Exception as e:
        print(f"Error getting session state: {e}")
        return jsonify({'error': str(e)}), 500