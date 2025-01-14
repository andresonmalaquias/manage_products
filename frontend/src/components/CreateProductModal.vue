<template>
  <b-modal
      v-model="localVisible"
      :title="isEditMode ? 'Editar Produto' : 'Cadastrar Produto'"
      @hide="onClose"
      @ok="handleSubmit"
  >
    <b-form>
      <b-form-group label="Nome">
        <b-form-input
            v-model="product.name"
            :state="validationState.name"
            placeholder="Nome do produto..."
            required
        />
        <b-form-invalid-feedback v-if="errors.name">
          {{ errors.name }}
        </b-form-invalid-feedback>
      </b-form-group>

      <b-form-group label="Descrição">
        <b-form-textarea
            v-model="product.description"
            :state="validationState.description"
            placeholder="Descrição do produto..."
        />
      </b-form-group>

      <b-form-group label="Preço">
        <b-form-input
            v-model="product.price"
            type="number"
            :state="validationState.price"
            placeholder="Preço do produto..."
            required
        />
        <b-form-invalid-feedback v-if="errors.price">
          {{ errors.price }}
        </b-form-invalid-feedback>
      </b-form-group>

      <b-form-group label="Data de Validade">
        <CustomDatepicker
            label="Data de Validade"
            id="expirationDate"
            v-model="product.expiration_date"
            :state="validationState.expiration_date"
            required
        />
      </b-form-group>

      <b-form-group label="Imagem">
        <CustomFileInput
            v-model="product.image"
            :state="validationState.image"
            label="Imagem do Produto"
            id="productImage"
        />
      </b-form-group>

      <b-form-group label="Categoria">
        <b-form-select
            v-model="product.category_id"
            :state="validationState.category_id"
            :options="categories"
            text-field="name"
            value-field="id"
            required
        />
        <b-form-invalid-feedback v-if="errors.category_id">
          {{ errors.category_id }}
        </b-form-invalid-feedback>
      </b-form-group>
    </b-form>
  </b-modal>

</template>

<script lang="ts">
import { ref, defineComponent, computed, onMounted } from 'vue';
import productService from '@/services/productService';
import CategoryService from '@/services/categoryService';
import { useToastService } from '@/services/toastService';
import { Product } from '@/types/Product';
import CustomDatepicker from "@/components/CustomDatepicker.vue";
import CustomFileInput from "@/components/CustomFileInput.vue";

export default defineComponent({
  name: 'CreateProductModal',
  components:{
    CustomDatepicker,
    CustomFileInput,
  },
  props: {
    isVisible: {
      type: Boolean,
      required: true,
    },
  },
  emits: ['create', 'cancel', 'update:isVisible'],

  setup(props, { emit }) {
    const product = ref<Product>({
      id: 0,
      name: '',
      description: '',
      price: 0,
      expiration_date: '',
      image: null,
      category_id: 0,
    });

    const validationState = ref<Record<string, boolean | null>>({
      name: null,
      description: null,
      price: null,
      expiration_date: null,
      image: null,
      category_id: null,
    });
    const categories = ref([]);
    const errors = ref<Record<string, string>>({});
    const { addToast } = useToastService();
    const isEditMode = ref(false);

    const localVisible = computed({
      get: () => props.isVisible,
      set: (value: boolean) => emit('update:isVisible', value),
    });

    onMounted(async () => {
      try {
        const response = await CategoryService.getCategories();
        categories.value = response.data.data;
      } catch (error) {
        addToast({
          title: 'Erro!',
          message: 'Não foi possível carregar as categorias.',
          variant: 'danger',
        });
      }
    });

    const openModalForCreate = () => {
      validationState.value = {};
      product.value = {
        id: 0,
        name: '',
        description: '',
        price: 0,
        expiration_date: '',
        image: null,
        category_id: 0,
      };
      isEditMode.value = false;
      localVisible.value = true;
    };

    const openModalForEdit = (productData: Product) => {
      validationState.value = {};
      product.value = { ...productData };
      validationState.value = {};
      isEditMode.value = true;
      localVisible.value = true;
    };

    const handleCancel = () => {
      emit('cancel');
      localVisible.value = false;
    };

    const onClose = () => {
      localVisible.value = false;
    };

    const validateForm = () => {
      errors.value = {};
      let isValid = true;

      if (!product.value.name) {
        errors.value.name = 'O nome é obrigatório.';
        validationState.value.name = false;
        isValid = false;
      } else {
        validationState.value.name = true;
      }

      if (product.value.price <= 0) {
        errors.value.price = 'O preço deve ser maior que zero.';
        validationState.value.price = false;
        isValid = false;
      } else {
        validationState.value.price = true;
      }

      if (!product.value.expiration_date) {
        errors.value.expiration_date = 'A data de validade é obrigatória.';
        validationState.value.expiration_date = false;
        isValid = false;
      } else {
        validationState.value.expiration_date = true;
      }

      if (!product.value.category_id) {
        errors.value.category_id = 'A categoria é obrigatória.';
        validationState.value.category_id = false;
        isValid = false;
      } else {
        validationState.value.category_id = true;
      }

      return isValid;
    };

    const handleSubmit = async (event: Event) => {
      if (!validateForm()){
        event.preventDefault();
        return;
      }

      try {
        const formData = new FormData();
        formData.append('name', product.value.name);

        if (product.value.description)
          formData.append('description', product.value.description);

        formData.append('price', product.value.price.toString());
        formData.append('expiration_date', product.value.expiration_date);
        formData.append('category_id', product.value.category_id.toString());

        if (product.value.image) {
          formData.append('image', product.value.image);
        }

        let response;

        if (isEditMode.value) {
          response = await productService.updateProduct(product.value.id, formData);
        } else {
          response = await productService.createProduct(formData);
        }

        addToast({
          title: 'Sucesso!',
          message: `O produto foi ${isEditMode.value ? 'atualizado' : 'criado'} com sucesso.`,
          variant: 'success',
          duration: 5000,
        });
        emit('create', response.data);
        localVisible.value = false;
      } catch (error) {
        console.error(error);
        addToast({
          title: 'Erro!',
          message: `Falha ao ${isEditMode.value ? 'atualizar' : 'criar'} o produto. Tente novamente.`,
          variant: 'danger',
        });
      }
    };

    return {
      product,
      categories,
      validationState,
      errors,
      localVisible,
      isEditMode,
      openModalForCreate,
      openModalForEdit,
      handleCancel,
      onClose,
      validateForm,
      handleSubmit,
    };
  },
});
</script>
