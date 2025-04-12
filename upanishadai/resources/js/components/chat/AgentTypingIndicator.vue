<template>
    <div v-if="isTyping" class="text-blue-300 italic dot-typing px-4">AI is typing</div>
  </template>
  
  <script>
  export default {
    name: 'AgentTypingIndicator',
    props: {
      isTyping: {
        type: Boolean,
        default: false
      }
    }
  }
  </script>