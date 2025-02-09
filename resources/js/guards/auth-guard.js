// auth.js (Pinia store)
import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('auth_token') || null,
  }),
  actions: {
    async checkAuth() {
      try {
        const response = await axios.get('/api/user');
        this.user = response.data;
        return true;
      } catch (error) {
        this.clearAuth();
        return false;
      }
    },
    clearAuth() {
      this.user = null;
      this.token = null;
      localStorage.removeItem('auth_token');
    },
  },
});