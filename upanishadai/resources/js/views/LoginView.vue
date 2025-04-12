<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white flex items-center justify-center p-6">
      <div class="max-w-md w-full glass rounded-xl p-8 shadow-xl border border-gray-800">
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
            UpanishadAI
          </h1>
          <p class="text-gray-400 mt-2">Sign in to your account</p>
        </div>
        
        <form @submit.prevent="login" class="space-y-6">
          <div v-if="error" class="p-3 bg-red-500/20 text-red-400 rounded-lg text-sm">
            {{ error }}
          </div>
          
          <div>
            <label for="email" class="block text-gray-300 mb-2">Email</label>
            <input 
              v-model="form.email" 
              id="email" 
              type="email" 
              class="w-full px-4 py-3 rounded-lg bg-gray-900 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
              autocomplete="email"
            >
            <p v-if="errors.email" class="text-red-400 mt-1 text-sm">{{ errors.email }}</p>
          </div>
          
          <div>
            <label for="password" class="block text-gray-300 mb-2">Password</label>
            <input 
              v-model="form.password" 
              id="password" 
              type="password" 
              class="w-full px-4 py-3 rounded-lg bg-gray-900 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
              autocomplete="current-password"
            >
            <p v-if="errors.password" class="text-red-400 mt-1 text-sm">{{ errors.password }}</p>
          </div>
          
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input 
                v-model="form.remember" 
                id="remember" 
                type="checkbox" 
                class="w-4 h-4 text-blue-500 border-gray-600 rounded focus:ring-blue-500 bg-gray-700"
              >
              <label for="remember" class="ml-2 text-sm text-gray-400">Remember me</label>
            </div>
            <a href="#" class="text-sm text-blue-400 hover:text-blue-300">Forgot password?</a>
          </div>
          
          <button 
            type="submit" 
            class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg shadow-lg hover:from-blue-700 hover:to-indigo-700 transition-all font-medium"
            :disabled="isLoading"
          >
            <span v-if="isLoading">Signing in...</span>
            <span v-else>Sign In</span>
          </button>
        </form>
        
        <div class="mt-6 text-center">
          <p class="text-gray-400 text-sm">
            Don't have an account?
            <router-link to="/register" class="text-blue-400 hover:text-blue-300 ml-1">
              Sign up
            </router-link>
          </p>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { mapActions, mapGetters } from 'vuex';
  
  export default {
    name: 'LoginView',
    data() {
      return {
        form: {
          email: '',
          password: '',
          remember: false
        },
        errors: {},
        error: null
      };
    },
    computed: {
      ...mapGetters({
        isLoading: 'isLoading',
        isAuthenticated: 'auth/isAuthenticated'
      })
    },
    created() {
      // Redirect if already authenticated
      if (this.isAuthenticated) {
        this.$router.push('/dashboard');
      }
    },
    methods: {
      ...mapActions({
        loginAction: 'auth/login',
        setLoading: 'setLoading'
      }),
      async login() {
        this.error = null;
        this.errors = {};
        
        try {
          this.setLoading(true);
          await this.loginAction(this.form);
          this.$router.push('/dashboard');
        } catch (error) {
          console.error('Login error:', error);
          
          if (error.response?.data?.errors) {
            this.errors = error.response.data.errors;
          } else {
            this.error = error.response?.data?.message || 'Login failed. Please check your credentials.';
          }
        } finally {
          this.setLoading(false);
        }
      }
    }
  };
  </script>