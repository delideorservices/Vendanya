<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white">
      <header class="bg-black/40 backdrop-blur-sm border-b border-slate-800 py-4">
        <div class="container mx-auto px-6 flex justify-between items-center">
          <div class="flex items-center">
            <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
              UpanishadAI Dashboard
            </h1>
          </div>
          <nav>
            <button @click="logout" class="text-blue-400 hover:text-blue-300 transition-colors">
              Logout
            </button>
          </nav>
        </div>
      </header>
  
      <main class="container mx-auto px-6 py-8">
        <!-- User Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
          <div class="glass rounded-xl p-6">
            <h3 class="text-gray-400 text-sm mb-2">Current Level</h3>
            <div class="flex items-end">
              <span class="text-3xl font-bold text-blue-400">{{ stats?.level || 1 }}</span>
              <div class="ml-4 flex-1">
                <div class="w-full h-2 bg-gray-700 rounded-full overflow-hidden">
                  <div class="h-full bg-gradient-to-r from-blue-500 to-purple-500" :style="`width: ${xpProgress}%`"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-400 mt-1">
                  <span>{{ stats?.xp || 0 }} XP</span>
                  <span>{{ nextLevelXp }} XP needed</span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="glass rounded-xl p-6">
            <h3 class="text-gray-400 text-sm mb-2">Current Streak</h3>
            <div class="flex items-center">
              <span class="text-3xl font-bold text-yellow-400">{{ streak?.current_streak || 0 }}</span>
              <span class="ml-2 text-yellow-400">days</span>
              <div class="ml-4">
                <p class="text-xs text-gray-400">Max: {{ streak?.max_streak || 0 }} days</p>
              </div>
            </div>
          </div>
          
          <div class="glass rounded-xl p-6">
            <h3 class="text-gray-400 text-sm mb-2">Retry Tokens</h3>
            <div class="flex items-center">
              <span class="text-3xl font-bold text-green-400">{{ streak?.retry_tokens || 0 }}</span>
              <div class="ml-4">
                <p class="text-xs text-gray-400">Use these when you get stuck</p>
              </div>
            </div>
          </div>
          
          <div class="glass rounded-xl p-6">
            <h3 class="text-gray-400 text-sm mb-2">Questions Answered</h3>
            <div class="flex items-center">
              <span class="text-3xl font-bold text-purple-400">{{ stats?.questions_answered || 0 }}</span>
              <div class="ml-4">
                <p class="text-xs text-gray-400">Keep going!</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Actions Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
          <router-link to="/homework" class="glass rounded-xl p-6 hover:border-blue-500 border border-transparent transition-colors">
            <div class="flex items-center">
              <div class="bg-blue-500/20 p-3 rounded-lg">
                <Plus class="w-6 h-6 text-blue-400" />
              </div>
              <div class="ml-4">
                <h3 class="font-medium">New Homework</h3>
                <p class="text-sm text-gray-400 mt-1">Submit a question to get help</p>
              </div>
            </div>
          </router-link>
          
          <div class="glass rounded-xl p-6 hover:border-purple-500 border border-transparent transition-colors cursor-pointer" @click="showRewards = true">
            <div class="flex items-center">
              <div class="bg-purple-500/20 p-3 rounded-lg">
                <Gift class="w-6 h-6 text-purple-400" />
              </div>
              <div class="ml-4">
                <h3 class="font-medium">Rewards</h3>
                <p class="text-sm text-gray-400 mt-1">View your badges and achievements</p>
              </div>
            </div>
          </div>
          
          <div class="glass rounded-xl p-6 hover:border-yellow-500 border border-transparent transition-colors cursor-pointer" @click="showLeaderboard = true">
            <div class="flex items-center">
              <div class="bg-yellow-500/20 p-3 rounded-lg">
                <Trophy class="w-6 h-6 text-yellow-400" />
              </div>
              <div class="ml-4">
                <h3 class="font-medium">Leaderboard</h3>
                <p class="text-sm text-gray-400 mt-1">See how you rank against others</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Recent Activity Section -->
        <h2 class="text-xl font-semibold mb-6">Recent Activity</h2>
        
        <div class="glass rounded-xl p-6 mb-6">
          <div v-if="recentHomeworks.length > 0" class="space-y-4">
            <div 
              v-for="homework in recentHomeworks" 
              :key="homework.id" 
              class="p-4 bg-gray-800/50 rounded-lg border border-gray-700 hover:border-blue-500 transition-colors cursor-pointer"
              @click="viewHomework(homework.id)"
            >
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="font-medium">{{ homework.question }}</h3>
                  <p class="text-gray-400 text-sm mt-1">{{ homework.subject }}</p>
                </div>
                <span class="px-3 py-1 text-xs rounded-full" :class="getStatusClass(homework.status)">
                  {{ homework.status }}
                </span>
              </div>
              <p class="text-xs text-gray-500 mt-2">Submitted {{ formatDate(homework.created_at) }}</p>
            </div>
          </div>
          <div v-else class="text-center py-8 text-gray-400">
            <p>No recent activity. Submit your first homework!</p>
            <router-link to="/homework" class="mt-4 inline-block px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg shadow-lg hover:from-blue-700 hover:to-indigo-700 transition-all font-medium">
              Submit Homework
            </router-link>
          </div>
        </div>
      </main>
      
      <!-- Rewards Modal -->
      <div v-if="showRewards" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-gray-900 rounded-xl p-8 max-w-2xl w-full max-h-[80vh] overflow-y-auto">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Your Rewards</h2>
            <button @click="showRewards = false" class="text-gray-400 hover:text-white">
              <X class="w-6 h-6" />
            </button>
          </div>
          
          <div class="mb-8">
            <h3 class="text-lg font-medium mb-4">Badges</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
              <div 
                v-for="badge in badges" 
                :key="badge.id" 
                class="bg-gray-800 rounded-lg p-4 text-center"
              >
                <div class="w-16 h-16 mx-auto mb-3 flex items-center justify-center bg-gray-700 rounded-full">
                  <img v-if="badge.icon" :src="badge.icon" :alt="badge.name" class="w-10 h-10">
                  <Trophy v-else class="w-8 h-8 text-yellow-400" />
                </div>
                <h4 class="font-medium">{{ badge.name }}</h4>
                <p class="text-xs text-gray-400 mt-1">{{ badge.description }}</p>
              </div>
            </div>
          </div>
          
          <div>
            <h3 class="text-lg font-medium mb-4">Rewards</h3>
            <div class="space-y-3">
              <div 
                v-for="reward in rewards" 
                :key="reward.id" 
                class="bg-gray-800 rounded-lg p-4 flex items-center"
              >
                <div class="w-12 h-12 flex items-center justify-center bg-gray-700 rounded-full">
                  <img v-if="reward.icon" :src="reward.icon" :alt="reward.name" class="w-8 h-8">
                  <Gift v-else class="w-6 h-6 text-purple-400" />
                </div>
                <div class="ml-4">
                  <h4 class="font-medium">{{ reward.name }}</h4>
                  <p class="text-sm text-gray-400">{{ reward.description }}</p>
                </div>
                <div class="ml-auto">
                  <span class="px-3 py-1 bg-purple-500/20 text-purple-400 rounded-full text-sm">
                    x{{ reward.pivot.quantity }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Leaderboard Modal -->
      <div v-if="showLeaderboard" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-gray-900 rounded-xl p-8 max-w-2xl w-full max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-semibold">Leaderboard</h2>
          <button @click="showLeaderboard = false" class="text-gray-400 hover:text-white">
            <X class="w-6 h-6" />
          </button>
        </div>
        
        <div class="space-y-4">
          <div 
            v-for="(user, index) in leaderboard" 
            :key="user.id" 
            class="bg-gray-800 rounded-lg p-4 flex items-center"
            :class="{'border-2 border-yellow-500': user.user_id === currentUser?.id}"
          >
            <div class="w-8 h-8 flex items-center justify-center bg-gray-700 rounded-full text-center font-bold">
              {{ index + 1 }}
            </div>
            <div class="ml-4 flex-1">
              <h4 class="font-medium">{{ user.user.name }}</h4>
              <div class="flex items-center mt-1">
                <span class="text-sm text-blue-400">Level {{ user.level }}</span>
                <span class="mx-2 text-gray-500">•</span>
                <span class="text-sm text-gray-400">{{ user.xp }} XP</span>
              </div>
            </div>
            <div class="ml-auto">
              <Trophy v-if="index === 0" class="w-6 h-6 text-yellow-400" />
              <Trophy v-else-if="index === 1" class="w-6 h-6 text-gray-400" />
              <Trophy v-else-if="index === 2" class="w-6 h-6 text-amber-700" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import { Plus, Gift, Trophy, X } from 'lucide-vue-next';

export default {
  name: 'DashboardView',
  components: {
    Plus,
    Gift,
    Trophy,
    X
  },
  data() {
    return {
      showRewards: false,
      showLeaderboard: false
    };
  },
  computed: {
    ...mapGetters({
      currentUser: 'auth/user',
      stats: 'gamification/stats',
      streak: 'gamification/streak',
      rewards: 'gamification/rewards',
      badges: 'gamification/badges',
      leaderboard: 'gamification/leaderboard',
      recentHomeworks: 'homework/homeworks',
      xpProgress: 'gamification/xpProgress',
      nextLevelXp: 'gamification/nextLevelXp'
    })
  },
  created() {
    // Fetch user stats
    this.fetchUserStats();
    
    // Fetch leaderboard
    this.fetchLeaderboard();
    
    // Fetch recent homeworks
    this.fetchHomeworks();
    
    // Update streak
    this.updateStreak();
  },
  methods: {
    ...mapActions({
      fetchUserStats: 'gamification/fetchUserStats',
      fetchLeaderboard: 'gamification/fetchLeaderboard',
      fetchHomeworks: 'homework/fetchHomeworks',
      updateStreak: 'gamification/updateStreak',
      logoutAction: 'auth/logout'
    }),
    async logout() {
      try {
        await this.logoutAction();
        this.$router.push('/login');
      } catch (error) {
        console.error('Logout failed:', error);
      }
    },
    viewHomework(homeworkId) {
      const homework = this.recentHomeworks.find(hw => hw.id === homeworkId);
      
      if (homework && homework.chat_sessions && homework.chat_sessions.length > 0) {
        // Navigate to the chat session
        this.$router.push(`/chat/${homework.chat_sessions[0].id}`);
      } else {
        // Create a new chat session for this homework
        this.$router.push(`/homework/${homeworkId}`);
      }
    },
    getStatusClass(status) {
      const statusClasses = {
        'pending': 'bg-yellow-500/20 text-yellow-400',
        'in_progress': 'bg-blue-500/20 text-blue-400',
        'completed': 'bg-green-500/20 text-green-400',
        'closed': 'bg-gray-500/20 text-gray-400'
      };
      
      return statusClasses[status] || 'bg-gray-500/20 text-gray-400';
    },
    formatDate(dateString) {
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      }).format(date);
    }
  }
};
</script>