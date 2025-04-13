<template>
    <div class="glass p-6 rounded-xl">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium">Memory Match</h3>
        <button @click="close" class="text-gray-400 hover:text-white">
          <X class="w-5 h-5" />
        </button>
      </div>
      
      <div v-if="!gameCompleted" class="space-y-6">
        <div class="flex justify-between items-center">
          <p class="text-sm text-gray-400">Pairs found: {{ pairsFound }} / {{ totalPairs }}</p>
          <p class="text-sm text-gray-400">Moves: {{ moves }}</p>
        </div>
        <div class="grid grid-cols-4 gap-3">
        <div 
          v-for="(card, index) in cards" 
          :key="index"
          @click="flipCard(index)"
          class="relative h-20 w-full cursor-pointer transition-transform"
          :class="{'pointer-events-none': card.matched || card.flipped || isBlocked}"
        >
          <div 
            class="absolute inset-0 bg-gray-700 rounded-lg flex items-center justify-center transition-transform duration-300 backface-hidden"
            :class="{'rotate-y-180': card.flipped}"
          >
            <span class="text-gray-400 text-2xl">?</span>
          </div>
          <div 
            class="absolute inset-0 bg-indigo-700 rounded-lg flex items-center justify-center text-white text-xl transition-transform duration-300 backface-hidden rotate-y-180"
            :class="{'rotate-y-0': card.flipped}"
          >
            {{ card.value }}
          </div>
        </div>
      </div>
    </div>
    
    <div v-else class="text-center py-6">
      <lottie-player 
        src="https://assets5.lottiefiles.com/packages/lf20_jfcgtlgm.json" 
        background="transparent" 
        speed="1" 
        style="width: 150px; height: 150px; margin: 0 auto;" 
        autoplay
      ></lottie-player>
      
      <h3 class="text-xl font-bold mb-2">Congratulations!</h3>
      <p class="text-lg mb-2">You completed the memory game!</p>
      <p class="text-sm text-gray-400 mb-4">Moves: {{ moves }}</p>
      
      <div class="flex justify-center">
        <button 
          @click="restartGame" 
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors mr-3"
        >
          Play Again
        </button>
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
import LottiePlayer from '@lottiefiles/vue-lottie-player';
import { mapActions } from 'vuex';

export default {
  name: 'MemoryGame',
  components: {
    X,
    LottiePlayer
  },
  props: {
    config: {
      type: Object,
      default: () => ({
        pairs: ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']
      })
    },
    gameId: {
      type: [Number, String],
      default: 2
    }
  },
  data() {
    return {
      cards: [],
      flippedCards: [],
      moves: 0,
      pairsFound: 0,
      isBlocked: false,
      gameCompleted: false
    };
  },
  computed: {
    totalPairs() {
      return this.config.pairs.length;
    }
  },
  created() {
    this.initializeGame();
  },
  methods: {
    ...mapActions({
      completeGame: 'gamification/completeGame'
    }),
    initializeGame() {
      // Create card pairs
      const cardPairs = [...this.config.pairs, ...this.config.pairs];
      
      // Shuffle cards
      const shuffledCards = this.shuffleArray(cardPairs);
      
      // Create card objects
      this.cards = shuffledCards.map(value => ({
        value,
        flipped: false,
        matched: false
      }));
      
      // Reset game state
      this.flippedCards = [];
      this.moves = 0;
      this.pairsFound = 0;
      this.isBlocked = false;
      this.gameCompleted = false;
    },
    shuffleArray(array) {
      const newArray = [...array];
      for (let i = newArray.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [newArray[i], newArray[j]] = [newArray[j], newArray[i]];
      }
      return newArray;
    },
    flipCard(index) {
      // Prevent flipping if already flipped or blocked
      if (this.cards[index].flipped || this.isBlocked) return;
      
      // Flip the card
      this.cards[index].flipped = true;
      this.flippedCards.push(index);
      
      // Check if two cards are flipped
      if (this.flippedCards.length === 2) {
        this.moves++;
        this.checkMatch();
      }
    },
    checkMatch() {
      this.isBlocked = true;
      
      const [firstIndex, secondIndex] = this.flippedCards;
      const firstCard = this.cards[firstIndex];
      const secondCard = this.cards[secondIndex];
      
      // Check if cards match
      if (firstCard.value === secondCard.value) {
        // Mark as matched
        firstCard.matched = true;
        secondCard.matched = true;
        
        this.pairsFound++;
        
        // Check if game is completed
        if (this.pairsFound === this.totalPairs) {
          this.gameCompleted = true;
          
          // Calculate score based on moves and total pairs
          const score = Math.max(0, 100 - (this.moves - this.totalPairs) * 5);
          
          // Complete the game with the score
          this.completeGame({
            gameId: this.gameId,
            score
          });
        }
        
        // Reset for next pair
        this.flippedCards = [];
        this.isBlocked = false;
      } else {
        // Flip back after delay
        setTimeout(() => {
          firstCard.flipped = false;
          secondCard.flipped = false;
          this.flippedCards = [];
          this.isBlocked = false;
        }, 1000);
      }
    },
    restartGame() {
      this.initializeGame();
    },
    close() {
      this.$emit('close');
    }
  }
}
</script>

<style scoped>
.backface-hidden {
  backface-visibility: hidden;
}

.rotate-y-180 {
  transform: rotateY(180deg);
}

.rotate-y-0 {
  transform: rotateY(0deg);
}
</style>
