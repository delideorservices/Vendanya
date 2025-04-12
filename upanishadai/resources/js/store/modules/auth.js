import axios from 'axios';

export default {
  namespaced: true,
  
  state: {
    user: null,
    authenticated: false,
  },
  
  getters: {
    user: state => state.user,
    isAuthenticated: state => state.authenticated,
  },
  
  mutations: {
    SET_USER(state, user) {
      state.user = user;
      state.authenticated = !!user;
    },
  },
  
  actions: {
    async login({ commit, dispatch }, credentials) {
      try {
        // Reset CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Login
        const response = await axios.post('/api/login', credentials);
        
        // Set user
        commit('SET_USER', response.data.user);
        
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Login failed', { root: true });
        throw error;
      }
    },
    
    async register({ commit, dispatch }, userData) {
      try {
        // Reset CSRF token
        await axios.get('/sanctum/csrf-cookie');
        
        // Register
        const response = await axios.post('/api/register', userData);
        
        // Set user
        commit('SET_USER', response.data.user);
        
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Registration failed', { root: true });
        throw error;
      }
    },
    
    async logout({ commit, dispatch }) {
      try {
        // Logout
        await axios.post('/api/logout');
        
        // Clear user
        commit('SET_USER', null);
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Logout failed', { root: true });
        throw error;
      }
    },
    
    async fetchUser({ commit, dispatch }) {
      try {
        const response = await axios.get('/api/user');
        commit('SET_USER', response.data);
        return response;
      } catch (error) {
        commit('SET_USER', null);
        return null;
      }
    },
  },
};