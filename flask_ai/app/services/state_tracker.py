class StateTracker:
    def __init__(self):
        self.sessions = {}
    
    def initialize_session(self, session_id, user_id, agent_id, context):
        self.sessions[session_id] = {
            'user_id': user_id,
            'agent_id': agent_id,
            'context': context,
            'messages': [],
            'state': 'active',
            'created_at': self._get_timestamp(),
            'last_activity': self._get_timestamp(),
        }
    
    def add_message(self, session_id, message_id, sender_type, content):
        if session_id not in self.sessions:
            raise ValueError(f"Session {session_id} not found")
        
        message = {
            'id': message_id,
            'sender_type': sender_type,
            'content': content,
            'timestamp': self._get_timestamp(),
        }
        
        self.sessions[session_id]['messages'].append(message)
        self.sessions[session_id]['last_activity'] = self._get_timestamp()
    
    def update_session_state(self, session_id, key, value):
        if session_id not in self.sessions:
            raise ValueError(f"Session {session_id} not found")
        
        self.sessions[session_id][key] = value
        self.sessions[session_id]['last_activity'] = self._get_timestamp()
    
    def get_session_state(self, session_id):
        if session_id not in self.sessions:
            raise ValueError(f"Session {session_id} not found")
        
        return self.sessions[session_id]
    
    def get_all_sessions(self):
        return self.sessions
    
    def end_session(self, session_id):
        if session_id not in self.sessions:
            raise ValueError(f"Session {session_id} not found")
        
        self.sessions[session_id]['state'] = 'closed'
        self.sessions[session_id]['ended_at'] = self._get_timestamp()
    
    def _get_timestamp(self):
        from datetime import datetime
        return datetime.now().isoformat()