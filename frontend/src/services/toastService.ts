import { reactive } from 'vue';

interface Toast {
  title: string;
  message: string;
  variant: string;
  duration?: number;
}

const state = reactive({
  toasts: [] as Toast[],
});

const addToast = (toast: Toast) => {
  state.toasts.push(toast);

  setTimeout(() => {
    const index = state.toasts.indexOf(toast);
    if (index !== -1) {
      state.toasts.splice(index, 1);
    }
  }, toast.duration || 3000);
};

export const useToastService = () => {
  return {
    state,
    addToast,
  };
};
