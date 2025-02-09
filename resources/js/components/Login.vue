<template>
    <div class="container mt-5">
      <form @submit.prevent="handleLogin">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" v-model="form.email" class="form-control">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" v-model="form.password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
      </form>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        form: {
          email: '',
          password: ''
        }
      }
    },
    methods: {
      async handleLogin() {
        try {
          // First get CSRF cookie
          await axios.get('/sanctum/csrf-cookie');
          
          const response = await axios.post('/api/login', this.form);
          
          // Store user and token
          localStorage.setItem('auth_token', response.data.token);
          this.$store.commit('setUser', response.data.user);
          
          // Redirect to dashboard
          this.$router.push('/dashboard');
        } catch (error) {
          console.error('Login failed:', error.response.data);
        }
      }
    }
  }
  </script>