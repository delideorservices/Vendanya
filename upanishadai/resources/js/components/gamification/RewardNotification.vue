<template>
    <transition name="slide-up">
      <div v-if="show" class="fixed bottom-8 right-8 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg p-4 shadow-xl max-w-sm">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <img v-if="reward.icon" :src="reward.icon" :alt="reward.name" class="w-12 h-12">
            <Gift v-else class="w-12 h-12 text-white" />
          </div>
          <div class="ml-4">
            <h3 class="text-white font-bold">{{ reward.name }}</h3>
            <p class="text-indigo-200 text-sm">{{ reward.description }}</p>
            <p class="text-indigo-200 text-xs mt-2">+{{ reward.xp_value }} XP</p>
          </div>
          <button @click="close" class="ml-4 text-indigo-200 hover:text-white">
            <X class="w-5 h-5" />
          </button>
        </div>
      </div>
    </transition>
  </template>
  
  <script>
  import { Gift, X } from 'lucide-vue-next';
  
  export default {
    name: 'RewardNotification',
    components: {
      Gift,
      X
    },
    props: {
      reward: {
        type: Object,
        required: true
      },
      duration: {
        type: Number,
        default: 5000
      }
    },
    data() {
      return {
        show: true,
        timer: null
      };
    },
    mounted() {
      this.timer = setTimeout(() => {
        this.show = false;
      }, this.duration);
    },
    beforeUnmount() {
      clearTimeout(this.timer);
    },
    methods: {
      close() {
        this.show = false;
        clearTimeout(this.timer);
      }
    }
  }
  </script>