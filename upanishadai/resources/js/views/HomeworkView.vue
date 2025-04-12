<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white">
      <header class="bg-black/40 backdrop-blur-sm border-b border-slate-800 py-4">
        <div class="container mx-auto px-6 flex justify-between items-center">
          <h1 class="text-2xl font-bold text-blue-300">Submit Homework</h1>
          <router-link to="/dashboard" class="text-blue-400 hover:text-blue-300 transition-colors">
            Back to Dashboard
          </router-link>
        </div>
      </header>
  
      <main class="container mx-auto px-6 py-8">
        <div class="max-w-4xl mx-auto">
          <div class="glass rounded-xl p-8 mb-8">
            <h2 class="text-xl font-semibold mb-6">New Homework Submission</h2>
            
            <form @submit.prevent="submitHomework" class="space-y-6">
              <div>
                <label for="subject" class="block text-gray-300 mb-2">Subject</label>
                <select 
                  v-model="form.subject" 
                  id="subject" 
                  class="w-full px-4 py-3 rounded-lg bg-gray-900 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Select a subject</option>
                  <option value="math">Mathematics</option>
                  <option value="science">Science</option>
                  <option value="english">English</option>
                  <option value="history">History</option>
                  <option value="geography">Geography</option>
                  <option value="computer_science">Computer Science</option>
                </select>
                <p v-if="errors.subject" class="text-red-400 mt-1 text-sm">{{ errors.subject }}</p>
              </div>
              
              <div>
                <label for="question" class="block text-gray-300 mb-2">Question</label>
                <input 
                  v-model="form.question" 
                  id="question" 
                  type="text" 
                  class="w-full px-4 py-3 rounded-lg bg-gray-900 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Enter your homework question"
                >
                <p v-if="errors.question" class="text-red-400 mt-1 text-sm">{{ errors.question }}</p>
              </div>
              
              <div>
                <label for="content" class="block text-gray-300 mb-2">Details</label>
                <textarea 
                  v-model="form.content" 
                  id="content" 
                  rows="6" 
                  class="w-full px-4 py-3 rounded-lg bg-gray-900 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                  placeholder="Provide additional details or context for your question"
                ></textarea>
                <p v-if="errors.content" class="text-red-400 mt-1 text-sm">{{ errors.content }}</p>
              </div>
              
              <div class="flex justify-end">
                <button 
                  type="submit" 
                  class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg shadow-lg hover:from-blue-700 hover:to-indigo-700 transition-all font-medium"
                  :disabled="isSubmitting"
                >
                  <span v-if="isSubmitting">Submitting...</span>
                  <span v-else>Submit Homework</span>
                </button>
              </div>
            </form>
          </div>
          
          <div v-if="recentSubmissions.length > 0" class="glass rounded-xl p-8">
            <h2 class="text-xl font-semibold mb-6">Recent Submissions</h2>
            
            <div class="space-y-4">
              <div 
                v-for="submission in recentSubmissions" 
                :key="submission.id" 
                class="p-4 bg-gray-800/50 rounded-lg border border-gray-700 hover:border-blue-500 transition-colors cursor-pointer"
                @click="viewSubmission(submission.id)"
              >
                <div class="flex justify-between items-start">
                  <div>
                    <h3 class="font-medium text-lg">{{ submission.question }}</h3>
                    <p class="text-gray-400 text-sm mt-1">{{ submission.subject }}</p>
                  </div>
                  <span class="px-3 py-1 text-xs rounded-full" :class="getStatusClass(submission.status)">
                    {{ submission.status }}
                  </span>
                </div>
                <p class="mt-2 text-gray-300 line-clamp-2">{{ submission.content }}</p>
                <p class="text-xs text-gray-500 mt-2">Submitted {{ formatDate(submission.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </template>
  
  <script>
  import { mapActions, mapGetters } from 'vuex';
  
  export default {
    name: 'HomeworkView',
    data() {
      return {
        form: {
          subject: '',
          question: '',
          content: ''
        },
        errors: {},
        isSubmitting: false
      };
    },
    computed: {
      ...mapGetters({
        recentSubmissions: 'homework/homeworks'
      })
    },
    created() {
      // Fetch recent homework submissions
      this.fetchHomeworks();
    },
    methods: {
      ...mapActions({
        fetchHomeworks: 'homework/fetchHomeworks',
        submitHomeworkAction: 'homework/submitHomework'
      }),
      async submitHomework() {
        // Reset errors
        this.errors = {};
        
        // Validate form
        if (!this.form.subject) {
          this.errors.subject = 'Please select a subject';
        }
        
        if (!this.form.question) {
          this.errors.question = 'Please enter your question';
        } else if (this.form.question.length > 500) {
          this.errors.question = 'Question is too long (max 500 characters)';
        }
        
        if (!this.form.content) {
          this.errors.content = 'Please provide details for your question';
        }
        
        // If there are validation errors, stop submission
        if (Object.keys(this.errors).length > 0) {
          return;
        }
        
        // Submit the homework
        this.isSubmitting = true;
        
        try {
          const response = await this.submitHomeworkAction(this.form);
          
          // Reset form
          this.form = {
            subject: '',
            question: '',
            content: ''
          };
          
          // Navigate to chat session for this homework
          this.$router.push(`/chat/${response.data.chat_session_id}`);
        } catch (error) {
          console.error('Failed to submit homework:', error);
          
          // Handle server-side validation errors
          if (error.response?.data?.errors) {
            this.errors = error.response.data.errors;
          }
        } finally {
          this.isSubmitting = false;
        }
      },
      viewSubmission(homeworkId) {
        // Find the chat session for this homework
        const homework = this.recentSubmissions.find(hw => hw.id === homeworkId);
        
        if (homework && homework.chat_sessions && homework.chat_sessions.length > 0) {
          // Navigate to the first chat session for this homework
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
          year: 'numeric',
          hour: 'numeric',
          minute: 'numeric'
        }).format(date);
      }
    }
  };
  </script>