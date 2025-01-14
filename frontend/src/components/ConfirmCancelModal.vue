<template>
  <b-modal v-model="localVisible" @hide="onClose" @ok="confirm" @cancel="cancel">
    <template #modal-title>
      {{ title || 'Confirmar ação' }}
    </template>

    <p class="mb-4">
      {{ message || 'Você tem certeza que deseja cancelar a operação?' }}
    </p>
  </b-modal>
</template>

<script lang="ts">
import { defineComponent, computed } from 'vue';

export default defineComponent({
  name: 'ConfirmCancelModal',
  props: {
    isVisible: {
      type: Boolean,
      required: true,
    },
    title: {
      type: String,
      default: '',
    },
    message: {
      type: String,
      default: '',
    },
  },
  emits: ['confirm', 'cancel', 'update:isVisible'],
  setup(props, { emit }) {
    const localVisible = computed({
      get: () => props.isVisible,
      set: (value: boolean) => emit('update:isVisible', value),
    });

    const confirm = () => {
      emit('confirm');
      localVisible.value = false;
    };

    const cancel = () => {
      emit('cancel');
      localVisible.value = false;
    };

    const onClose = () => {
      localVisible.value = false;
    };

    return {
      localVisible,
      confirm,
      cancel,
      onClose,
    };
  },
});
</script>
