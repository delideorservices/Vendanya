import axios from 'axios';

export default {
  namespaced: true,
  
  state: {
    stats: null,
    streak: null,
    rewards: [],
    badges: [],
    leaderboard: [],
    miniGames: [],
  },
  
  getters: {
    stats: state => state.stats,
    streak: state => state.streak,
    rewards: state => state.rewards,
    badges: state => state.badges,
    leaderboard: state => state.leaderboard,
    miniGames: state => state.miniGames,
    level: state => state.stats?.level || 1,
    xp: state => state.stats?.xp || 0,
    nextLevelXp: state => (state.stats?.level * 100) || 100,
    xpProgress: state => {
      if (!state.stats) return 0;
      const currentLevelXp = (state.stats.level - 1) * 100;
      const nextLevelXp = state.stats.level * 100;
      const levelProgress = state.stats.xp - currentLevelXp;
      const levelRange = nextLevelXp - currentLevelXp;
      return (levelProgress / levelRange) * 100;
    },
  },
  
  mutations: {
    SET_USER_STATS(state, { stats, streak, rewards, badges }) {
      state.stats = stats;
      state.streak = streak;
      state.rewards = rewards;
      state.badges = badges;
    },
    SET_LEADERBOARD(state, leaderboard) {
      state.leaderboard = leaderboard;
    },
    SET_MINI_GAMES(state, games) {
      state.miniGames = games;
    },
    UPDATE_STREAK(state, streak) {
      state.streak = streak;
    },
  },
  
  actions: {
    async fetchUserStats({ commit, dispatch }) {
      try {
        const response = await axios.get('/api/gamification/stats');
        commit('SET_USER_STATS', response.data);
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to fetch user stats', { root: true });
        throw error;
      }
    },
    
    async fetchLeaderboard({ commit, dispatch }) {
      try {
        const response = await axios.get('/api/gamification/leaderboard');
        commit('SET_LEADERBOARD', response.data.leaderboard);
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to fetch leaderboard', { root: true });
        throw error;
      }
    },
    
    async fetchMiniGames({ commit, dispatch }) {
      try {
        const response = await axios.get('/api/gamification/mini-games');
        commit('SET_MINI_GAMES', response.data.mini_games);
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to fetch mini-games', { root: true });
        throw error;
      }
    },
    
    async earnXP({ commit, dispatch }, { amount, source }) {
      try {
        const response = await axios.post('/api/gamification/xp', { amount, source });
        
        // Refresh user stats to get updated values
        dispatch('fetchUserStats');
        
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to earn XP', { root: true });
        throw error;
      }
    },
    
    async updateStreak({ commit, dispatch }) {
      try {
        const response = await axios.post('/api/gamification/streak');
        commit('UPDATE_STREAK', response.data.streak);
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to update streak', { root: true });
        throw error;
      }
    },
    
    async useRetryToken({ commit, dispatch }) {
      try {
        const response = await axios.post('/api/gamification/retry-token/use');
        commit('UPDATE_STREAK', response.data.streak);
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to use retry token', { root: true });
        throw error;
      }
    },
    
    async completeGame({ commit, dispatch }, { gameId, score }) {
      try {
        const response = await axios.post(`/api/gamification/games/${gameId}/complete`, { score });
        
        // Refresh user stats to get updated values
        dispatch('fetchUserStats');
        
        return response;
      } catch (error) {
        dispatch('setError', error.response?.data?.message || 'Failed to complete game', { root: true });
        throw error;
      }
    },
  },
};