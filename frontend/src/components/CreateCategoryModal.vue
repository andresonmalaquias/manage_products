<template>
  <b-modal
      v-model="localVisible"
      :title="isEditMode ? 'Editar Categoria' : 'Cadastrar Categoria'"
      @hide="onClose"
      @ok="handleSubmit"
  >
    <b-form>
      <b-form-group label="Nome">
        <b-form-input
            v-model="categoryName"
            :state="validationState.name"
            placeholder="Categoria..."
            required
        />
        <b-form-invalid-feedback v-if="errors.name">
          {{ errors.name }}
        </b-form-invalid-feedback>
      </b-form-group>
    </b-form>
  </b-modal>
</template>

<script lang="ts">
import { defineComponent, ref, computed } from 'vue';
import CategoryService from '@/services/categoryService';
import { useToastService } from '@/services/toastService';

export default defineComponent({
  name: 'CreateCategoryModal',
  props: {
    isVisible: {
      type: Boolean,
      required: true,
    },
  },
  emits: ['create', 'cancel', 'update:isVisible'],

  setup(props, { emit }) {
    const categoryName = ref('');
    const categoryId = ref<number>(0);
    const { addToast } = useToastService();
    const isEditMode = ref(false);
    const errors = ref<Record<string, string>>({});

    const validationState = ref<Record<string, boolean | null>>({
      name: null,
    });

    const localVisible = computed({
      get: () => props.isVisible,
      set: (value: boolean) => emit('update:isVisible', value),
    });

    const validateForm = () => {
      errors.value = {};
      let isValid = true;

      if (!categoryName.value) {
        errors.value.name = 'O nome da categoria é obrigatório.';
        validationState.value.name = false;
        isValid = false;
      } else {
        validationState.value.name = true;
      }

      return isValid;
    };

    const handleSubmit = async (event: Event) => {
      if (!validateForm()) {
        event.preventDefault()
        return;
      }

      try {
        let response;

        if (isEditMode.value) {
          response = await CategoryService.updateCategory(categoryId.value, { name: categoryName.value });
        } else {
          response = await CategoryService.createCategory({ name: categoryName.value });
        }

        addToast({
          title: 'Sucesso!',
          message: `A categoria foi ${isEditMode.value ? 'atualizada' : 'criada'} com sucesso.`,
          variant: 'success',
          duration: 5000,
        });
        emit('create', response.data);
        localVisible.value = false;
      } catch (error) {
        console.error(error);
        addToast({
          title: 'Erro!',
          message: `Falha ao ${isEditMode.value ? 'atualizar' : 'criar'} a categoria. Tente novamente.`,
          variant: 'danger',
        });
      }
    };

    const openModalForCreate = () => {
      validationState.value = {};
      categoryName.value = '';
      categoryId.value = 0;
      isEditMode.value = false;
      localVisible.value = true;
    };

    const openModalForEdit = (category: { id: number; name: string }) => {
      validationState.value = {};
      categoryId.value = category.id;
      categoryName.value = category.name;
      isEditMode.value = true;
      localVisible.value = true;
    };

    const handleCancel = () => {
      emit('cancel');
      localVisible.value = false;
      categoryName.value = '';
      categoryId.value = 0;
    };

    const onClose = () => {
      localVisible.value = false;
      categoryName.value = '';
    };

    return {
      categoryName,
      localVisible,
      isEditMode,
      validationState,
      errors,
      handleSubmit,
      openModalForCreate,
      openModalForEdit,
      handleCancel,
      onClose,
    };
  },
});
</script>
