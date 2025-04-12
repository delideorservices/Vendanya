import authModule from './modules/auth';
import homeworkModule from './modules/homework';
import chatModule from './modules/chat';
import gamificationModule from './modules/gamification';

export default {
  state: {
    loading: false,
    error: null,
  },
  
  getters: {
    isLoading: state => state.loading,
    hasError: state => !!state.error,
    error: state => state.error,
  },
  
  mutations: {
    SET_LOADING(state, status) {
      state.loading = status;
    },
    SET_ERROR(state, error) {
      state.error = error;
    },
    CLEAR_ERROR(state) {
      state.error = null;
    },
  },
  
  actions: {
    setLoading({ commit }, status) {
      commit('SET_LOADING', status);
    },
    setError({ commit }, error) {
      commit('SET_ERROR', error);
    },
    clearError({ commit }) {
      commit('CLEAR_ERROR');
    },
  },
  
  modules: {
    auth: authModule,
    homework: homeworkModule,
    chat: chatModule,
    gamification: gamificationModule,
  }
};