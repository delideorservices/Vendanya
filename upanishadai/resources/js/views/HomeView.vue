<template>
    <div class="home-container">
      <header class="header glass">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
          <div class="flex items-center">
            <div class="text-gradient text-2xl font-bold">UpanishadAI</div>
          </div>
          <nav>
            <ul class="flex space-x-6">
              <li v-if="!isAuthenticated">
                <router-link to="/login" class="nav-link">Login</router-link>
              </li>
              <li v-if="!isAuthenticated">
                <router-link to="/register" class="nav-link highlight">Get Started</router-link>
              </li>
              <li v-if="isAuthenticated">
                <router-link to="/dashboard" class="nav-link">Dashboard</router-link>
              </li>
              <li v-if="isAuthenticated">
                <button @click="logout" class="nav-link">Logout</button>
              </li>
            </ul>
          </nav>
        </div>
      </header>
  
      <main class="main-content">
        <section class="hero">
          <div class="container mx-auto px-6 py-16 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-8">
              <span class="text-gradient">AI-Powered</span> Homework Help
            </h1>
            <p class="text-xl md:text-2xl max-w-3xl mx-auto mb-12 text-gray-300">
              UpanishadAI transforms homework help with interactive AI tutors, instant feedback, and fun gamification.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-6">
              <router-link v-if="!isAuthenticated" to="/register" class="cta-button primary">
                Get Started Now
              </router-link>
              <router-link v-else to="/homework" class="cta-button primary">
                Submit Homework
              </router-link>
              <a href="#features" class="cta-button secondary">
                Learn More
              </a>
            </div>
          </div>
        </section>
  
        <section id="features" class="features bg-black/20">
          <div class="container mx-auto px-6 py-20">
            <h2 class="text-3xl md:text-4xl font-bold mb-16 text-center">Key Features</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
              <div class="feature-card glass">
                <div class="icon-container">
                  <Brain class="w-10 h-10 text-blue-400" />
                </div>
                <h3 class="text-xl font-semibold mb-3">AI-Driven Learning</h3>
                <p class="text-gray-300">
                  Dynamic AI tutors that personalize their approach based on your learning style.
                </p>
              </div>
              
              <div class="feature-card glass">
                <div class="icon-container">
                  <Zap class="w-10 h-10 text-yellow-400" />
                </div>
                <h3 class="text-xl font-semibold mb-3">Real-time Feedback</h3>
                <p class="text-gray-300">
                  Get instant, detailed feedback on your homework and test your understanding.
                </p>
              </div>
              
              <div class="feature-card glass">
                <div class="icon-container">
                  <Trophy class="w-10 h-10 text-purple-400" />
                </div>
                <h3 class="text-xl font-semibold mb-3">Gamified Experience</h3>
                <p class="text-gray-300">
                  Earn rewards, badges, and keep your learning streak to stay motivated.
                </p>
              </div>
            </div>
          </div>
        </section>
      </main>
  
      <footer class="footer glass">
        <div class="container mx-auto px-6 py-8">
          <div class="text-center">
            <p class="text-sm text-gray-400">
              © {{ new Date().getFullYear() }} UpanishadAI. All rights reserved.
            </p>
          </div>
        </div>
      </footer>
    </div>
  </template>
  <script>
  import { mapGetters, mapActions } from 'vuex';
  import { Brain, Zap, Trophy } from 'lucide-vue-next';
  
  export default {
    name: 'HomeView',
    components: {
      Brain,
      Zap,
      Trophy
    },
    computed: {
      ...mapGetters({
        isAuthenticated: 'auth/isAuthenticated'
      })
    },
    methods: {
      ...mapActions({
        logoutAction: 'auth/logout'
      }),
      async logout() {
        try {
          await this.logoutAction();
          this.$router.push('/login');
        } catch (error) {
          console.error('Logout failed:', error);
        }
      }
    }
  }
  </script>
  
  <style scoped>
  .text-gradient {
    background: linear-gradient(to right, #3B82F6, #8B5CF6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  
  .nav-link {
    color: var(--text-secondary-color);
    font-weight: 500;
    transition: all 0.3s ease;
  }
  
  .nav-link:hover {
    color: var(--text-color);
  }
  
  .nav-link.highlight {
    background: linear-gradient(to right, #3B82F6, #8B5CF6);
    padding: 0.5rem 1.5rem;
    border-radius: 9999px;
    color: white;
  }
  
  .cta-button {
    padding: 1rem 2rem;
    border-radius: 9999px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-align: center;
  }
  
  .cta-button.primary {
    background: linear-gradient(to right, #3B82F6, #8B5CF6);
    color: white;
    box-shadow: 0 4px 20px rgba(79, 70, 229, 0.3);
  }
  
  .cta-button.secondary {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
  }
  
  .cta-button:hover {
    transform: translateY(-3px);
  }
  
  .feature-card {
    padding: 2rem;
    border-radius: 1rem;
    transition: all 0.3s ease;
  }
  
  .feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  }
  
  .icon-container {
    margin-bottom: 1.5rem;
  }
  </style>