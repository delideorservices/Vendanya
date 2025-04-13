<template>
    <transition name="slide-up">
      <div v-if="show" class="fixed inset-0 flex items-center justify-center z-50 bg-black/70">
        <div class="bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl p-8 max-w-md w-full text-center shadow-2xl">
          <lottie-player 
            src="https://assets9.lottiefiles.com/packages/lf20_touohxv0.json" 
            background="transparent" 
            speed="1" 
            style="width: 200px; height: 200px; margin: 0 auto;" 
            autoplay
          ></lottie-player>
          <h2 class="text-3xl font-bold text-white mb-2">Level Up!</h2>
          <p class="text-xl text-indigo-200 mb-6">You've reached level {{ newLevel }}</p>
          <button 
            @click="close" 
            class="px-8 py-3 bg-white text-indigo-700 rounded-full font-bold shadow-xl hover:bg-indigo-100 transition-colors"
          >
            Continue
          </button>
        </div>
      </div>
    </transition>
  </template>
  
  <script>
  import LottiePlayer from '@lottiefiles/vue-lottie-player';
  
  export default {
    name: 'LevelUpNotification',
    components: {
      LottiePlayer
    },
    props: {
      newLevel: {
        type: Number,
        required: true
      }
    },
    data() {
      return {
        show: true
      };
    },
    methods: {
      close() {
        this.show = false;
      }
    }
  }
  </script>