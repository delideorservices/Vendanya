<template>
    <div 
      :class="[
        isUser ? 'bg-slate-800 border-l-4 border-blue-500' : 'bg-gradient-to-r from-indigo-700 to-purple-800 border-l-4 border-indigo-400',
        'chat-bubble'
      ]"
    >
      <span v-if="isUser">👧 <strong>Student:</strong> </span>
      <span v-else>🧙‍♂️ <strong>AI:</strong> </span>
      {{ content }}
    </div>
  </template>
  
  <script>
  export default {
    name: 'ChatMessage',
    props: {
      sender: {
        type: String,
        required: true,
      },
      content: {
        type: String,
        required: true,
      },
    },
    computed: {
      isUser() {
        return this.sender === 'user';
      }
    }
  }
  </script>