<template>
    <div class="glass p-6 rounded-xl">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium">Quick Quiz</h3>
        <button @click="close" class="text-gray-400 hover:text-white">
          <X class="w-5 h-5" />
        </button>
      </div>
      
      <div v-if="currentQuestionIndex < questions.length">
        <div class="mb-6">
          <p class="text-lg mb-4">{{ currentQuestion.question }}</p>
          <div class="space-y-3">
            <div 
              v-for="(option, index) in currentQuestion.options" 
              :key="index"
              @click="selectOption(index)"
              class="p-3 rounded-lg cursor-pointer transition-colors"
              :class="getOptionClass(index)"
            >
              {{ option }}
            </div>
          </div>
        </div>
        
        <div v-if="selectedOption !== null && !isLastQuestion" class="flex justify-end">
          <button 
            @click="nextQuestion" 
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            Next Question
          </button>
        </div>
        
        <div v-if="selectedOption !== null && isLastQuestion" class="flex justify-end">
          <button 
            @click="finishQuiz" 
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
          >
            Finish Quiz
          </button>
        </div>
      </div>
      
      <div v-else class="text-center py-6">
        <lottie-player 
          v-if="score / questions.length >= 0.7"
          src="https://assets5.lottiefiles.com/packages/lf20_jfcgtlgm.json" 
          background="transparent" 
          speed="1" 
          style="width: 150px; height: 150px; margin: 0 auto;" 
          autoplay
        ></lottie-player>
        
        <h3 class="text-xl font-bold mb-2">Quiz Complete!</h3>
        <p class="text-lg mb-4">Your score: {{ score }} / {{ questions.length }}</p>
        
        <div class="flex justify-center">
          <button 
            @click="close" 
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { X } from 'lucide-vue-next';
  import { LottiePlayer } from '@lottiefiles/vue-lottie-player';
  import { mapActions } from 'vuex';
  
  export default {
    name: 'QuizGame',
    components: {
      X,
      LottiePlayer
    },
    props: {
      config: {
        type: Object,
        default: () => ({
          questions: [
            {
              question: 'What is the capital of France?',
              options: ['Berlin', 'Madrid', 'Paris', 'Rome'],
              correctIndex: 2
            },
            {
              question: 'Which planet is known as the Red Planet?',
              options: ['Venus', 'Mars', 'Jupiter', 'Saturn'],
              correctIndex: 1
            },
            {
              question: 'What is 2 + 2?',
              options: ['3', '4', '5', '22'],
              correctIndex: 1
            }
          ]
        })
      },
      gameId: {
        type: [Number, String],
        default: 1
      }
    },
    data() {
      return {
        currentQuestionIndex: 0,
        selectedOption: null,
        score: 0,
        questions: []
      };
    },
    computed: {
      currentQuestion() {
        return this.questions[this.currentQuestionIndex];
      },
      isLastQuestion() {
        return this.currentQuestionIndex === this.questions.length - 1;
      }
    },
    created() {
      // Initialize questions from config
      this.questions = this.config.questions || [];
    },
    methods: {
      ...mapActions({
        completeGame: 'gamification/completeGame'
      }),
      selectOption(index) {
        if (this.selectedOption !== null) return;
        
        this.selectedOption = index;
        
        // Check if correct
        if (index === this.currentQuestion.correctIndex) {
          this.score++;
        }
      },
      nextQuestion() {
        this.currentQuestionIndex++;
        this.selectedOption = null;
      },
      finishQuiz() {
        // Calculate score percentage for game completion
        const scorePercentage = (this.score / this.questions.length) * 100;
        
        // Complete the game with the score
        this.completeGame({
          gameId: this.gameId,
          score: scorePercentage
        });
        
        // Move to results
        this.currentQuestionIndex = this.questions.length;
      },
      getOptionClass(index) {
        if (this.selectedOption === null) {
          return 'bg-gray-800 hover:bg-gray-700';
        }
        
        if (index === this.currentQuestion.correctIndex) {
          return 'bg-green-600/30 border border-green-500';
        }
        
        if (index === this.selectedOption && index !== this.currentQuestion.correctIndex) {
          return 'bg-red-600/30 border border-red-500';
        }
        
        return 'bg-gray-800 opacity-50';
      },
      close() {
        this.$emit('close');
      }
    }
  }
  </script>