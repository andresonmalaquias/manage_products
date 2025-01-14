<template>
  <div class="login-container">
    <b-container class="d-flex justify-content-center align-items-center vh-100">
      <b-card class="p-4 shadow-sm login-card">
        <h5 class="text-center mb-4 text-muted">Login</h5>

        <b-form>
          <b-form-group>
            <b-form-input
                v-model="email"
                type="text"
                placeholder="Email"
                class="mb-3"
                :state="validationState.email"
                required
            ></b-form-input>
            <b-form-invalid-feedback v-if="errors.email">
              {{ errors.email }}
            </b-form-invalid-feedback>
          </b-form-group>

          <b-form-group>
            <b-form-input
                v-model="password"
                type="password"
                placeholder="Password"
                class="mb-3"
                :state="validationState.password"
                required
            ></b-form-input>
            <b-form-invalid-feedback v-if="errors.password">
              {{ errors.password }}
            </b-form-invalid-feedback>
          </b-form-group>

          <b-button type="submit" variant="dark" block :disabled="loading" @click="handleLogin">
            <b-spinner v-if="loading" small></b-spinner>
            <span v-if="!loading">Sign in</span>
          </b-button>

          <b-alert v-if="loginError" variant="danger" class="mt-3">
            Informações incorretas. Tente novamente.
          </b-alert>
        </b-form>
      </b-card>
    </b-container>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import AuthService from "@/services/authService";
import { useRouter } from 'vue-router';

export default defineComponent({
  name: 'LoginView',

  setup() {
    const email = ref('');
    const password = ref('');
    const router = useRouter();
    const loading = ref(false);
    const loginError = ref(false);
    const errors = ref<Record<string, string>>({});
    const validationState = ref<Record<string, boolean | null>>({
      email: null,
      password: null,
    });

    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

    const passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;

    const validateForm = () => {
      errors.value = {};
      let isValid = true;

      if (!emailPattern.test(email.value)) {
        errors.value.email = 'Email inválido.';
        validationState.value.email = false;
        isValid = false;
      } else {
        validationState.value.email = true;
      }

      if (!passwordPattern.test(password.value)) {
        errors.value.password = 'Senha inválida.';
        validationState.value.password = false;
        isValid = false;
      } else {
        validationState.value.password = true;
      }

      return isValid;
    };

    const handleLogin = async (event: Event) => {
      if (!validateForm()) {
        event.preventDefault();
        return;
      }

      loading.value = true;
      loginError.value = false;

      try {
        await AuthService.login(email.value, password.value);
        router.push('/category');
      } catch (error) {
        loginError.value = true;
        console.error('Erro no login', error);
      } finally {
        loading.value = false;
      }
    };

    return {
      email,
      password,
      loading,
      loginError,
      validationState,
      errors,
      handleLogin,
    };
  },
});
</script>

<style scoped>
.login-container {
  background-color: #f1f3f5;
  height: 100vh;
  padding: 15px;
}

.login-card {
  max-width: 340px;
  border-radius: 8px;
  background-color: #ffffff;
}
</style>
