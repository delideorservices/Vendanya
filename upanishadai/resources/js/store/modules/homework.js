import axios from 'axios';

export default {
  namespaced: true,
  
  state: {
    homeworks: [],
    currentHomework: null,
  },
  
  getters: {
    homeworks: state => state.homeworks,
    currentHomework: state => state.currentHomework,
  },
  
  mutations: {
    SET_HOMEWORKS(state, homeworks) {
      state.homeworks = homeworks;
    },
    SET_CURRENT_HOMEWORK(state, homework) {
      state.currentHomework = homework;
    },
    ADD_HOMEWORK(state, homework) {
      state.homeworks.unshift(homework);
    },
    UPDATE_HOMEWORK(state, updatedHomework) {
      const index = state.homeworks.findIndex(hw => hw.id === updatedHomework.id);
      if (index !== -1) {
        state.homeworks.splice(index, 1, updatedHomework);
      }
      
      if (state.currentHomework?.id === updatedHomework.id) {
        state.currentHomework = updatedHomework;
      }
    },
    REMOVE_HOMEWORK(state, homeworkId) {
      state.homeworks = state.homeworks.filter(hw => hw.id !== homeworkId);
      
      if (state.currentHomework?.id === homeworkId) {
        state.currentHomework = null;
      }
    },
  },
  
  actions: {
    async fetchHomeworks({ commit, dispatch }) {
      try {
        dispatch('setLoading', true, { root: true });
        const response = await axios.get('/api/homework');
        commit('SET_HOMEWORKS', response.data.homeworks);
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to fetch homeworks', { root: true });
        throw error;
      } finally {
        dispatch('setLoading', false, { root: true });
      }
    },
    
    async fetchHomework({ commit, dispatch }, homeworkId) {
      try {
        dispatch('setLoading', true, { root: true });
        const response = await axios.get(`/api/homework/${homeworkId}`);
        commit('SET_CURRENT_HOMEWORK', response.data.homework);
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to fetch homework', { root: true });
        throw error;
      } finally {
        dispatch('setLoading', false, { root: true });
      }
    },
    
    async submitHomework({ commit, dispatch }, homeworkData) {
      try {
        dispatch('setLoading', true, { root: true });
        const response = await axios.post('/api/homework', homeworkData);
        commit('ADD_HOMEWORK', response.data.homework);
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to submit homework', { root: true });
        throw error;
      } finally {
        dispatch('setLoading', false, { root: true });
      }
    },
    
    async updateHomework({ commit, dispatch }, { homeworkId, homeworkData }) {
      try {
        dispatch('setLoading', true, { root: true });
        const response = await axios.put(`/api/homework/${homeworkId}`, homeworkData);
        commit('UPDATE_HOMEWORK', response.data.homework);
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to update homework', { root: true });
        throw error;
      } finally {
        dispatch('setLoading', false, { root: true });
      }
    },
    
    async deleteHomework({ commit, dispatch }, homeworkId) {
      try {
        dispatch('setLoading', true, { root: true });
        await axios.delete(`/api/homework/${homeworkId}`);
        commit('REMOVE_HOMEWORK', homeworkId);
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to delete homework', { root: true });
        throw error;
      } finally {
        dispatch('setLoading', false, { root: true });
      }
    },
  },
};