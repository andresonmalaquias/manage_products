import { ref } from 'vue';
import api from './api';
import router from '@/router';

class AuthService {
  private authenticated = ref(!!localStorage.getItem('access_token'));

  async login(email: string, password: string) {
    try {
      const response = await api.post('login/', { email, password });
      const accessToken = response.data.access_token;

      localStorage.setItem('access_token', accessToken);
      this.authenticated.value = true;

      return response.data;
    } catch (error) {
      console.error('Erro ao realizar o login:', error);
      throw error;
    }
  }

  logout() {
    localStorage.removeItem('access_token');
    this.authenticated.value = false;
    router.push('/');
  }

  isAuthenticated() {
    return this.authenticated.value;
  }

  getAuthState() {
    return this.authenticated;
  }
}

export default new AuthService();
