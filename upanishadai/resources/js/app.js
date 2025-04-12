import './bootstrap';
import { createApp } from 'vue';
import { createStore } from 'vuex';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import routes from './router';
import storeConfig from './store';
import { registerLucideIcons } from './utils/icons';

// Create Vuex store
const store = createStore(storeConfig);

// Create Vue Router
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Create Vue app
const app = createApp(App);

// Register Lucide icons
registerLucideIcons(app);

// Use Vuex and Vue Router
app.use(store);
app.use(router);

// Mount the app
app.mount('#app');