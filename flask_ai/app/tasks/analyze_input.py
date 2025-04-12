class AnalyzeInput:
    def run(self, message, memory):
        """
        Analyze the user input to determine the intent, subject, difficulty, etc.
        
        In a real implementation, this would use an LLM to analyze the message.
        For now, we'll use a simple heuristic approach.
        """
        # Extract subject from context if available
        subject = memory.get('subject', self._detect_subject(message))
        
        # Detect intent
        intent = self._detect_intent(message)
        
        # Estimate difficulty
        difficulty = self._estimate_difficulty(message)
        
        # Detect emotional state
        emotional_state = self._detect_emotional_state(message)
        
        # Detect need for visualization
        need_visualization = self._needs_visualization(message)
        
        # Confidence level (would normally be provided by the model)
        confidence = 0.85
        
        return {
            'subject': subject,
            'intent': intent,
            'difficulty': difficulty,
            'emotional_state': emotional_state,
            'need_visualization': need_visualization,
            'confidence': confidence
        }
    
    def _detect_subject(self, message):
        """Detect the subject of the message"""
        # Simple keyword matching - in real implementation, this would use NER or topic modeling
        subjects = {
            'math': ['math', 'equation', 'algebra', 'geometry', 'calculus', 'number'],
            'science': ['science', 'physics', 'chemistry', 'biology', 'experiment'],
            'history': ['history', 'war', 'civilization', 'century', 'ancient'],
            'english': ['english', 'grammar', 'essay', 'literature', 'shakespeare'],
            'geography': ['geography', 'map', 'country', 'continent', 'climate'],
        }
        
        message_lower = message.lower()
        
        for subject, keywords in subjects.items():
            for keyword in keywords:
                if keyword in message_lower:
                    return subject
        
        return 'general'
    
    def _detect_intent(self, message):
        """Detect the intent of the message"""
        message_lower = message.lower()
        
        # Check for question intent
        if '?' in message or any(q in message_lower for q in ['what', 'how', 'why', 'when', 'where', 'can you', 'could you']):
            return 'question'
        
        # Check for confirmation intent
        if any(conf in message_lower for conf in ['yes', 'correct', 'right', 'exactly', 'i understand', 'got it']):
            return 'confirm_understanding'
        
        # Check for confusion intent
        if any(conf in message_lower for conf in ['confused', "don't understand", 'difficult', 'hard', 'not sure', 'unclear']):
            return 'express_confusion'
        
        # Default intent
        return 'inform'
    
    def _estimate_difficulty(self, message):
        """Estimate the difficulty level of the message"""
        message_lower = message.lower()
        
        # Check for explicit mentions of difficulty
        if any(easy in message_lower for easy in ['easy', 'simple', 'basic', 'beginner']):
            return 'easy'
        
        if any(hard in message_lower for hard in ['hard', 'difficult', 'advanced', 'complex', 'challenging']):
            return 'hard'
        
        # Check message length as a proxy for complexity
        if len(message.split()) > 20:
            return 'hard'
        elif len(message.split()) < 10:
            return 'easy'
        
        # Default difficulty
        return 'medium'
    
    def _detect_emotional_state(self, message):
        """Detect the emotional state of the user"""
        message_lower = message.lower()
        
        # Check for frustration
        if any(frust in message_lower for frust in ['frustrated', 'annoyed', 'angry', 'upset', 'stuck', 'confusing']):
            return 'frustrated'
        
        # Check for excitement
        if any(excit in message_lower for excit in ['exciting', 'cool', 'awesome', 'interesting', 'love', 'enjoy']):
            return 'excited'
        
        # Check for anxiety
        if any(anx in message_lower for anx in ['worried', 'anxious', 'nervous', 'scared', 'afraid', 'stress']):
            return 'anxious'
        
        # Default emotional state
        return 'neutral'
    
    def _needs_visualization(self, message):
        """Determine if the message indicates a need for visualization"""
        message_lower = message.lower()
        
        # Check for explicit requests for visualization
        if any(vis in message_lower for vis in ['show', 'diagram', 'picture', 'visual', 'graph', 'draw', 'image']):
            return True
        
        # Check for subjects that often benefit from visualization
        if any(vis_subj in message_lower for vis_subj in ['geometry', 'graph', 'map', 'chart', 'plot', 'cycle', 'process']):
            return True
        
        return False
