<template>
    <transition name="slide-up">
      <div v-if="show" class="fixed bottom-8 right-8 bg-gradient-to-r from-yellow-600 to-amber-600 rounded-lg p-4 shadow-xl max-w-sm">
        <div class="flex items-start">
          <div class="flex-shrink-0 bg-amber-700 p-2 rounded-full">
            <img v-if="badge.icon" :src="badge.icon" :alt="badge.name" class="w-12 h-12">
            <Trophy v-else class="w-12 h-12 text-yellow-300" />
          </div>
          <div class="ml-4">
            <h3 class="text-white font-bold">New Badge: {{ badge.name }}</h3>
            <p class="text-amber-200 text-sm">{{ badge.description }}</p>
          </div>
          <button @click="close" class="ml-4 text-amber-200 hover:text-white">
            <X class="w-5 h-5" />
          </button>
        </div>
      </div>
    </transition>
  </template>
  
  <script>
  import { Trophy, X } from 'lucide-vue-next';
  
  export default {
    name: 'BadgeNotification',
    components: {
      Trophy,
      X
    },
    props: {
      badge: {
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