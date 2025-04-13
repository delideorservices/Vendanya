<template>
  <!-- Keep your existing login form HTML -->
  <div class="min-h-screen flex items-center justify-center bg-gray-900">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg max-w-md w-full">
      <h1 class="text-2xl font-bold text-blue-400 text-center mb-6">UpanishadAI</h1>
      <h2 class="text-xl text-white text-center mb-6">Sign in to your account</h2>
      
      <form @submit.prevent="login">
        <div class="mb-4">
          <label for="email" class="block text-gray-300 mb-2">Email</label>
          <input 
            v-model="form.email" 
            type="email" 
            id="email" 
            class="w-full bg-gray-700 text-white px-4 py-2 rounded border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          >
        </div>
        
        <div class="mb-6">
          <label for="password" class="block text-gray-300 mb-2">Password</label>
          <input 
            v-model="form.password" 
            type="password" 
            id="password" 
            class="w-full bg-gray-700 text-white px-4 py-2 rounded border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          >
          <div class="flex justify-between items-center mt-2">
            <div class="flex items-center">
              <input 
                v-model="form.remember" 
                type="checkbox" 
                id="remember" 
                class="text-blue-600 bg-gray-700 border-gray-600 focus:ring-blue-500"
              >
              <label for="remember" class="ml-2 text-sm text-gray-400">Remember me</label>
            </div>
            <router-link to="/forgot-password" class="text-sm text-blue-400 hover:text-blue-300">
              Forgot password?
            </router-link>
          </div>
        </div>
        
        <button 
          type="submit" 
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
          :disabled="loading"
        >
          <span v-if="loading">Signing in...</span>
          <span v-else>Sign in</span>
        </button>
      </form>
      
      <div class="mt-6 text-center">
        <p class="text-sm text-gray-400">
          Don't have an account?
          <router-link to="/register" class="text-blue-400 hover:text-blue-300">
            Register
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'LoginView',
  data() {
    return {
      form: {
        email: '',
        password: '',
        remember: false
      },
      loading: false,
      error: null
    };
  },
  methods: {
    async login() {
      this.loading = true;
      this.error = null;
      
      try {
        // Get CSRF cookie
        await axios.get('/sanctum/csrf-cookie');
        
        // Submit login request
        const response = await axios.post('/api/login', this.form);
        
        // Store auth token if using Sanctum
        // localStorage.setItem('token', response.data.token);
        
        // Redirect to dashboard
        this.$router.push('/dashboard');
      } catch (error) {
        console.error('Login error:', error);
        this.error = error.response?.data?.message || 'Failed to login. Please check your credentials.';
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>