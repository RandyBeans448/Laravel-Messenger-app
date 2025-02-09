// router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      component: () => import('@/components/Login.vue'),
      meta: { guestOnly: true }, // Only for logged-out users
    },
  ],
});

// Navigation Guard
router.beforeEach(async (to) => {
  const authStore = useAuthStore();
  const isAuthenticated = await authStore.checkAuth();

  // Redirect to login if route requires auth and user isn't authenticated
  if (to.meta.requiresAuth && !isAuthenticated) {
    return '/login';
  }
});

export default router;