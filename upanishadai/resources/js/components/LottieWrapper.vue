<template>
    <div ref="container" class="lottie-container" :style="containerStyle"></div>
  </template>
  
  <script>
  export default {
    name: 'LottieWrapper',
    props: {
      src: {
        type: String,
        required: true
      },
      autoplay: {
        type: Boolean,
        default: true
      },
      loop: {
        type: Boolean,
        default: true
      },
      width: {
        type: [String, Number],
        default: '100%'
      },
      height: {
        type: [String, Number],
        default: '100%'
      },
      speed: {
        type: Number,
        default: 1
      }
    },
    computed: {
      containerStyle() {
        return {
          width: typeof this.width === 'number' ? `${this.width}px` : this.width,
          height: typeof this.height === 'number' ? `${this.height}px` : this.height
        };
      }
    },
    mounted() {
      // Import Lottie dynamically to avoid the error
      import('@lottiefiles/lottie-player').then(module => {
        // Wait for the next tick to ensure the component is mounted
        this.$nextTick(() => {
          try {
            const player = document.createElement('lottie-player');
            player.src = this.src;
            player.autoplay = this.autoplay;
            player.loop = this.loop;
            player.speed = this.speed;
            
            // Set dimensions
            player.style.width = '100%';
            player.style.height = '100%';
            
            // Clear the container and append the player
            const container = this.$refs.container;
            container.innerHTML = '';
            container.appendChild(player);
            
            // Add event listeners
            player.addEventListener('complete', () => this.$emit('complete'));
            player.addEventListener('play', () => this.$emit('play'));
            player.addEventListener('pause', () => this.$emit('pause'));
            player.addEventListener('ready', () => this.$emit('ready'));
            
            // Store the player reference
            this.player = player;
          } catch (error) {
            console.error('Error initializing Lottie player:', error);
          }
        });
      }).catch(error => {
        console.error('Error loading Lottie library:', error);
      });
    },
    beforeUnmount() {
      // Clean up
      if (this.player) {
        this.player.remove();
      }
    }
  };
  </script>
  
  <style scoped>
  .lottie-container {
    display: inline-block;
    overflow: hidden;
  }
  </style>