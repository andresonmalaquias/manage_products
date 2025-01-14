<template>
  <div class="toast-container">
    <b-toast
        :title="teste"
        :variant="success"
        toaster="b-toaster-top-center"
        :auto-hide= "true"
        :auto-hide-delay="3000"
        visible
    >
      {{ 'teste' }}
    </b-toast>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { useToastService } from '@/services/toastService';

export default defineComponent({
  name: 'GlobalToast',
  setup() {
    const state = useToastService();

    return {
      state,
    };
  },
});
</script>

<style>
.toast-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 1050;
}
</style>
