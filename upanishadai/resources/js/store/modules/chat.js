import axios from 'axios';

export default {
  namespaced: true,
  
  state: {
    sessions: [],
    currentSession: null,
    messages: [],
    agentTyping: false,
    activeComponents: {},
  },
  
  getters: {
    sessions: state => state.sessions,
    currentSession: state => state.currentSession,
    messages: state => state.messages,
    isAgentTyping: state => state.agentTyping,
    activeComponents: state => state.activeComponents,
  },
  
  mutations: {
    SET_SESSIONS(state, sessions) {
      state.sessions = sessions;
    },
    SET_CURRENT_SESSION(state, session) {
      state.currentSession = session;
    },
    SET_MESSAGES(state, messages) {
      state.messages = messages;
    },
    ADD_MESSAGE(state, message) {
      state.messages.push(message);
    },
    SET_AGENT_TYPING(state, status) {
      state.agentTyping = status;
    },
    ACTIVATE_COMPONENT(state, { component, config }) {
      state.activeComponents = {
        ...state.activeComponents,
        [component]: config || true,
      };
    },
    DEACTIVATE_COMPONENT(state, component) {
      const newComponents = { ...state.activeComponents };
      delete newComponents[component];
      state.activeComponents = newComponents;
    },
    CLEAR_ACTIVE_COMPONENTS(state) {
      state.activeComponents = {};
    },
  },
  
  actions: {
    async fetchSessions({ commit, dispatch }) {
      try {
        const response = await axios.get('/api/chat/sessions');
        commit('SET_SESSIONS', response.data.sessions);
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to fetch chat sessions', { root: true });
        throw error;
      }
    },
    
    async fetchSession({ commit, dispatch }, sessionId) {
      try {
        dispatch('setLoading', true, { root: true });
        const response = await axios.get(`/api/chat/sessions/${sessionId}`);
        commit('SET_CURRENT_SESSION', response.data.session);
        
        // Also fetch messages for this session
        dispatch('fetchMessages', sessionId);
        
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to fetch chat session', { root: true });
        throw error;
      } finally {
        dispatch('setLoading', false, { root: true });
      }
    },
    
    async createSession({ commit, dispatch }, { homeworkId, agentId }) {
      try {
        dispatch('setLoading', true, { root: true });
        const response = await axios.post('/api/chat/sessions', {
          homework_id: homeworkId,
          agent_id: agentId,
        });
        
        // Add to sessions list and set as current
        const newSession = response.data.chat_session;
        commit('SET_SESSIONS', [...state.sessions, newSession]);
        commit('SET_CURRENT_SESSION', newSession);
        commit('SET_MESSAGES', []);
        
        // Set up Echo listener for this session
        dispatch('setupChatListeners', newSession.id);
        
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to create chat session', { root: true });
        throw error;
      } finally {
        dispatch('setLoading', false, { root: true });
      }
    },
    
    async fetchMessages({ commit, dispatch }, sessionId) {
      try {
        const response = await axios.get(`/api/chat/sessions/${sessionId}/messages`);
        commit('SET_MESSAGES', response.data.messages);
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to fetch messages', { root: true });
        throw error;
      }
    },
    
    async sendMessage({ commit, dispatch, state }, { sessionId, content }) {
      try {
        const response = await axios.post(`/api/chat/sessions/${sessionId}/messages`, { content });
        
        // Add user message to the list
        commit('ADD_MESSAGE', response.data.chat_message);
        
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to send message', { root: true });
        throw error;
      }
    },
    
    async endSession({ commit, dispatch, state }, sessionId) {
      try {
        const response = await axios.put(`/api/chat/sessions/${sessionId}/end`);
        
        // Update session in list
        const updatedSessions = state.sessions.map(session => {
          if (session.id === sessionId) {
            return { ...session, status: 'closed' };
          }
          return session;
        });
        
        commit('SET_SESSIONS', updatedSessions);
        
        // Update current session if needed
        if (state.currentSession?.id === sessionId) {
          commit('SET_CURRENT_SESSION', { ...state.currentSession, status: 'closed' });
        }
        
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to end chat session', { root: true });
        throw error;
      }
    },
    
    setupChatListeners({ commit, dispatch }, sessionId) {
      // Listen for new messages
      window.Echo.private(`chat.${sessionId}`)
        .listen('MessageSent', (e) => {
          commit('ADD_MESSAGE', e);
        })
        .listen('AgentTyping', (e) => {
          commit('SET_AGENT_TYPING', e.isTyping);
          
          // Auto-reset typing status after a delay
          if (e.isTyping) {
            setTimeout(() => {
              commit('SET_AGENT_TYPING', false);
            }, 30000); // 30 seconds timeout
          }
        })
        .listen('UIComponentTriggered', (e) => {
          if (e.type === 'trigger') {
            commit('ACTIVATE_COMPONENT', {
              component: e.component,
              config: e.config,
            });
          }
        });
        
      // Listen for gamification events
      window.Echo.private(`user.${this.$store.state.auth.user.id}`)
        .listen('RewardEarned', (e) => {
          // Show reward notification
          commit('ACTIVATE_COMPONENT', {
            component: 'reward_notification',
            config: {
              reward: e,
              timestamp: e.timestamp,
            },
          });
          
          // Update gamification state
          dispatch('gamification/fetchUserStats', null, { root: true });
        })
        .listen('BadgeEarned', (e) => {
          // Show badge notification
          commit('ACTIVATE_COMPONENT', {
            component: 'badge_notification',
            config: {
              badge: e,
              timestamp: e.timestamp,
            },
          });
          
          // Update gamification state
          dispatch('gamification/fetchUserStats', null, { root: true });
        })
        .listen('LevelUp', (e) => {
          // Show level up notification
          commit('ACTIVATE_COMPONENT', {
            component: 'level_up_notification',
            config: {
              newLevel: e.new_level,
              timestamp: e.timestamp,
            },
          });
          
          // Update gamification state
          dispatch('gamification/fetchUserStats', null, { root: true });
        });
    },
    
    cleanupChatListeners({ commit }, sessionId) {
      window.Echo.leave(`chat.${sessionId}`);
      commit('CLEAR_ACTIVE_COMPONENTS');
    },
  },
};