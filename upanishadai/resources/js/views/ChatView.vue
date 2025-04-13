<template>
    <div class="flex h-screen overflow-hidden">
      <!-- Sidebar Tabs -->
      <aside class="w-72 bg-black/40 backdrop-blur-md shadow-xl border-r border-slate-800 p-6 overflow-y-auto">
        <h2 class="text-2xl font-bold mb-6 text-blue-300 tracking-wider">Learning Modules</h2>
        <ul class="space-y-4">
          <li v-for="(module, index) in learningModules" :key="index">
            <button 
              @click="activateModule(module.id)" 
              class="w-full text-left px-4 py-3 glass tab-button" 
              :class="module.textClass + ' rounded-lg'"
            >
              <component :is="module.icon" class="w-5 h-5"></component>
              {{ module.name }}
            </button>
          </li>
        </ul>
      </aside>
  
      <!-- Main Chat Panel -->
      <main class="flex-1 flex flex-col relative bg-black/30 backdrop-blur-xl">
        <!-- Celebration Animation -->
        <div id="celebration" class="absolute inset-0 flex items-center justify-center" v-if="showingCelebration">
          <lottie-player 
            src="https://assets1.lottiefiles.com/packages/lf20_jbrw3hcz.json" 
            background="transparent" 
            speed="1" 
            style="width: 300px; height: 300px" 
            autoplay
            @complete="showingCelebration = false"
          ></lottie-player>
        </div>
  
        <!-- Chat Messages -->
        <div class="flex-1 overflow-y-auto px-10 py-6 space-y-6" ref="messagesContainer">
          <template v-if="messages.length > 0">
            <div 
              v-for="message in messages" 
              :key="message.id" 
              :class="[
                message.sender_type.includes('User') ? 
                  'bg-slate-800 border-l-4 border-blue-500' : 
                  'bg-gradient-to-r from-indigo-700 to-purple-800 border-l-4 border-indigo-400',
                'chat-bubble'
              ]"
            >
              <span v-if="message.sender_type.includes('User')">👧 <strong>Student:</strong> </span>
              <span v-else>🧙‍♂️ <strong>AI:</strong> </span>
              {{ message.content }}
            </div>
          </template>
          <div v-else class="text-center text-gray-400 py-10">
            No messages yet. Start the conversation!
          </div>
          
          <div v-if="isAgentTyping" class="text-blue-300 italic dot-typing px-4">AI is typing</div>
        </div>
  
        <!-- Input Box -->
        <footer class="bg-black/40 border-t border-slate-700 p-4 flex items-center space-x-4">
          <input 
            v-model="newMessage" 
            @keyup.enter="sendMessage"
            type="text" 
            placeholder="Engage with the AI..." 
            class="flex-1 px-5 py-3 border border-blue-600 bg-black text-white rounded-lg shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500" 
          />
          <button 
            @click="sendMessage"
            class="bg-gradient-to-br from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-full font-bold shadow-lg transition-all"
          >
            Transmit 🚀
          </button>
        </footer>
      </main>
    </div>
  </template>
  
  <script>
  import { mapGetters, mapActions, mapMutations } from 'vuex';
  import { 
    FileText, Gamepad, BookOpen, Volume2, 
    Book, Laugh, Sparkles, Trophy 
  } from 'lucide-vue-next';
  // import LottiePlayer from '@lottiefiles/vue-lottie-player';
  
  export default {
    name: 'ChatView',
    components: {
      // LottiePlayer,
      FileText, Gamepad, BookOpen, Volume2, 
      Book, Laugh, Sparkles, Trophy
    },
    props: {
      sessionId: {
        type: [String, Number],
        required: true
      }
    },
    data() {
      return {
        newMessage: '',
        showingCelebration: false,
        learningModules: [
          { id: 'quiz', name: 'Quiz Tab', icon: 'FileText', textClass: 'text-blue-100' },
          { id: 'minigames', name: 'MiniGames Tab', icon: 'Gamepad', textClass: 'text-green-100' },
          { id: 'pdf', name: 'PDF Tab', icon: 'BookOpen', textClass: 'text-yellow-100' },
          { id: 'audio', name: 'Audio Explanation Tab', icon: 'Volume2', textClass: 'text-purple-100' },
          { id: 'story', name: 'Story Tab', icon: 'Book', textClass: 'text-pink-100' },
          { id: 'joke', name: 'Joke Tab', icon: 'Laugh', textClass: 'text-red-100' },
          { id: 'celebrations', name: 'Celebrations Tab', icon: 'Sparkles', textClass: 'text-indigo-100' },
          { id: 'leaderboard', name: 'Leaderboard Tab', icon: 'Trophy', textClass: 'text-gray-100' },
        ]
      };
    },
    computed: {
      ...mapGetters({
        currentSession: 'chat/currentSession',
        messages: 'chat/messages',
        isAgentTyping: 'chat/isAgentTyping',
        activeComponents: 'chat/activeComponents'
      })
    },
    watch: {
      messages() {
        this.$nextTick(() => {
          this.scrollToBottom();
        });
      },
      activeComponents: {
        handler(newComponents) {
          // Handle active components
          if (newComponents.celebrations) {
            this.showingCelebration = true;
          }
          
          // Handle other component activations as needed
        },
        deep: true
      }
    },
    created() {
      // Fetch session data and messages
      this.fetchSession(this.sessionId);
      
      // Set up WebSocket listeners
      this.setupChatListeners(this.sessionId);
    },
    beforeUnmount() {
      // Clean up listeners when component is destroyed
      this.cleanupChatListeners(this.sessionId);
    },
    mounted() {
    // This is where you add the code
    window.Echo.channel(`chat.${this.sessionId}`)
      .listen('MessageSent', (e) => {
          this.messages.push(e.message);
          this.scrollToBottom();
      })
      .listen('AgentTyping', (e) => {
          this.isAgentTyping = e.isTyping;
          
          // Auto reset typing indicator after 30 seconds
          if (this.typingTimer) {
              clearTimeout(this.typingTimer);
          }
          
          this.typingTimer = setTimeout(() => {
              this.isAgentTyping = false;
          }, 30000);
      });
      
    // If you have other mounted code like fetching initial messages
    this.fetchMessages();
  },
    methods: {
      ...mapActions({
        fetchSession: 'chat/fetchSession',
        sendMessageAction: 'chat/sendMessage',
        setupChatListeners: 'chat/setupChatListeners',
        cleanupChatListeners: 'chat/cleanupChatListeners'
      }),
      ...mapMutations({
        activateComponent: 'chat/ACTIVATE_COMPONENT',
        deactivateComponent: 'chat/DEACTIVATE_COMPONENT'
      }),
      async sendMessage() {
        if (!this.newMessage.trim()) return;
        
        try {
          await this.sendMessageAction({
            sessionId: this.sessionId,
            content: this.newMessage
          });
          
          // Clear input after sending
          this.newMessage = '';
        } catch (error) {
          console.error('Failed to send message:', error);
        }
      },
      scrollToBottom() {
        if (this.$refs.messagesContainer) {
          this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
        }
      },
      activateModule(moduleId) {
        // Activate the selected module
        this.activateComponent({
          component: moduleId,
          config: {}
        });
        
        // In a real implementation, this would interact with the AI system
        // to change context or generate module-specific content
      }
    }
  };
  </script>
  