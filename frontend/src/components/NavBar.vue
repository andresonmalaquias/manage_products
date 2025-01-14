<template>
  <b-navbar toggleable="lg" type="light" variant="light" v-if="isAuthenticated">
    <b-container>
      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav>
          <b-nav-item :to="{ name: 'category' }">Categorias</b-nav-item>
          <b-nav-item :to="{ name: 'product' }">Produtos</b-nav-item>
        </b-navbar-nav>

        <b-navbar-nav class="ml-auto">
          <b-button variant="outline-danger" @click="handleLogout">Logout</b-button>
        </b-navbar-nav>
      </b-collapse>
    </b-container>
  </b-navbar>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import AuthService from '@/services/authService';

export default defineComponent({
  name: 'NavBar',
  setup() {
    const isAuthenticated = AuthService.getAuthState();

    const handleLogout = () => {
      AuthService.logout();
    };

    return {
      isAuthenticated,
      handleLogout,
    };
  },
});
</script>
