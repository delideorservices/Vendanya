import HomeView from '../views/HomeView.vue';
import HomeworkView from '../views/HomeworkView.vue';
import ChatView from '../views/ChatView.vue';
import DashboardView from '../views/DashboardView.vue';
import LoginView from '../views/LoginView.vue';
import RegisterView from '../views/RegisterView.vue';

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { guestOnly: true }
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterView,
    meta: { guestOnly: true }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: DashboardView,
    meta: { requiresAuth: true }
  },
  {
    path: '/homework',
    name: 'homework',
    component: HomeworkView,
    meta: { requiresAuth: true }
  },
  {
    path: '/chat/:sessionId',
    name: 'chat',
    component: ChatView,
    meta: { requiresAuth: true },
    props: true
  },
];

export default routes;