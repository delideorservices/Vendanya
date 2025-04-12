<template>
    <div class="app-container">
      <router-view></router-view>
    </div>
  </template>
  
  <script>
  export default {
    name: 'App',
  }
  </script>
  
  <style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
  
  :root {
    --primary-color: #3B82F6;
    --secondary-color: #8B5CF6;
    --accent-color: #EC4899;
    --bg-color: #111827;
    --bg-secondary-color: #1F2937;
    --text-color: #F9FAFB;
    --text-secondary-color: #D1D5DB;
    --border-color: #374151;
  }
  
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }
  
  body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(to bottom right, #111827, #0F172A, #0C0D21);
    color: var(--text-color);
    min-height: 100vh;
  }
  
  .app-container {
    min-height: 100vh;
  }
  
  .glass {
    background: rgba(255, 255, 255, 0.04);
    backdrop-filter: blur(14px);
    border: 1px solid rgba(255, 255, 255, 0.06);
  }
  
  .tab-button {
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .tab-button:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateX(6px);
  }
  
  .chat-bubble {
    border-radius: 1.25rem;
    padding: 1rem 1.5rem;
    line-height: 1.65;
    box-shadow: 0 6px 30px rgba(0, 0, 0, 0.2);
  }
  
  .dot-typing::after {
    content: ' .';
    animation: dots 1.5s steps(5, end) infinite;
  }
  
  @keyframes dots {
    0%, 20% { content: ' .'; }
    40% { content: ' ..'; }
    60% { content: ' ...'; }
    100% { content: ' .'; }
  }
  
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.3s ease;
  }
  
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
  
  .slide-up-enter-active,
  .slide-up-leave-active {
    transition: transform 0.4s ease, opacity 0.4s ease;
  }
  
  .slide-up-enter-from,
  .slide-up-leave-to {
    transform: translateY(20px);
    opacity: 0;
  }
  </style>