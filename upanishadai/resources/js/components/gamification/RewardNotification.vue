<template>
  <transition name="slide-up">
    <div v-if="show" class="fixed bottom-8 right-8 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg p-4 shadow-xl max-w-sm z-50">
      <div class="flex items-start">
        <div class="flex-shrink-0">
          <img v-if="reward.icon" :src="`/images/rewards/${reward.icon}`" :alt="reward.name" class="w-12 h-12">
          <div v-else class="w-12 h-12 bg-indigo-800 rounded-full flex items-center justify-center">
            <i data-lucide="gift" class="w-6 h-6 text-white"></i>
          </div>
        </div>
        <div class="ml-4">
          <h3 class="text-white font-bold">{{ reward.name }}</h3>
          <p class="text-indigo-200 text-sm">{{ reward.description }}</p>
          <p v-if="reward.xp_value > 0" class="text-indigo-200 text-xs mt-2">+{{ reward.xp_value }} XP</p>
        </div>
        <button @click="close" class="ml-4 text-indigo-200 hover:text-white">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'RewardNotification',
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
    // Auto-close after duration
    this.timer = setTimeout(() => {
      this.show = false;
    }, this.duration);
    
    // Initialize icons
    if (window.lucide) {
      window.lucide.createIcons();
    }
  },
  beforeUnmount() {
    clearTimeout(this.timer);
  },
  methods: {
    close() {
      this.show = false;
      this.$emit('close');
    }
  }
}
</script>

<style scoped>
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease;
}
.slide-up-enter-from,
.slide-up-leave-to {
  transform: translateY(30px);
  opacity: 0;
}
</style>