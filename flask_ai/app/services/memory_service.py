class MemoryService:
    def __init__(self):
        self.memories = {}
    
    def initialize_memory(self, session_id, initial_context=None):
        self.memories[session_id] = initial_context or {}
        self.memories[session_id]['conversation_history'] = []
    
    def get_memory(self, session_id):
        if session_id not in self.memories:
            self.initialize_memory(session_id)
        
        return self.memories[session_id]
    
    def update_memory(self, session_id, updates):
        if session_id not in self.memories:
            self.initialize_memory(session_id)
        
        for key, value in updates.items():
            if key == 'conversation_history' and 'conversation_history' in self.memories[session_id]:
                # Append to conversation history
                self.memories[session_id]['conversation_history'].append(value)
            else:
                # Update or add the key
                self.memories[session_id][key] = value
    
    def add_to_conversation_history(self, session_id, message):
        if session_id not in self.memories:
            self.initialize_memory(session_id)
        
        if 'conversation_history' not in self.memories[session_id]:
            self.memories[session_id]['conversation_history'] = []
        
        self.memories[session_id]['conversation_history'].append(message)
    
    def clear_memory(self, session_id):
        if session_id in self.memories:
            del self.memories[session_id]