<template>
  <ConfirmCancelModal
      :isVisible="confirmModalVisible"
      @update:isVisible="confirmModalVisible = $event"
      @ok="handleDelete"
      @cancel="cancelDelete"
      :title="'Confirmar Exclusão'"
      :message="'Você tem certeza que deseja excluir este produto?'"
  />

  <div class="product-list-container">
    <b-container fluid>
      <b-row class="align-items-center my-3">
        <b-col md="6" sm="12" class="mb-2 mb-md-0">
          <b-input-group>
            <b-form-input
                v-model="search"
                placeholder="Buscar produtos..."
                debounce="300"
            />
            <b-input-group-append>
              <b-button variant="outline-primary" @click="fetchProducts(true)">
                Buscar
              </b-button>
            </b-input-group-append>
          </b-input-group>
        </b-col>
        <b-col md="6" sm="12" class="text-md-end text-sm-start">
          <b-button variant="success" @click="openCreateModal">
            <i class="bi bi-plus-circle"></i> Adicionar Produto
          </b-button>
        </b-col>
      </b-row>

      <b-card class="shadow-sm mt-3">
        <b-card-header>
          <h5 class="mb-0">Lista de Produtos</h5>
        </b-card-header>
        <b-card-body>
          <b-table
              :items="products"
              :fields="fields"
              bordered
              striped
              hover
              responsive
              class="mb-3"
          >
            <template #cell(actions)="row">
              <b-button size="sm" variant="primary" @click="openEditModal(row.item)">
                <i class="bi bi-pencil"></i> Editar
              </b-button>
              <b-button size="sm" variant="danger" @click="confirmDelete(row.item.id)">
                <i class="bi bi-trash"></i> Excluir
              </b-button>
            </template>
          </b-table>

          <b-pagination
              v-model="currentPage"
              :total-rows="totalItems"
              :per-page="itemsPerPage"
              align="center"
              @click="fetchProducts()"
          />
        </b-card-body>
      </b-card>
    </b-container>

    <CreateProductModal
        ref="modalRef"
        v-model:isVisible="isCreateModalVisible"
        @create="fetchProducts"
        @cancel="closeCreateModal"
    />
  </div>
</template>

<script lang="ts">
import {defineComponent, ref, type Ref} from 'vue';
import {useToastService} from '@/services/toastService';
import CreateProductModal from '@/components/CreateProductModal.vue';
import ProductService from '@/services/productService';
import {Product} from "@/types/Product";
import ConfirmCancelModal from "@/components/ConfirmCancelModal.vue";

type CreateProductModalType = {
  openModalForCreate: () => void;
  openModalForEdit: (product: Product) => void;
};

export default defineComponent({
  name: 'ProductView',
  components: {
    ConfirmCancelModal,
    CreateProductModal,
  },
  setup() {
    const modalRef: Ref<CreateProductModalType | null> = ref(null);
    const products = ref([]);
    const totalItems = ref(0);
    const currentPage = ref(1);
    const lastPage = ref(1);
    const itemsPerPage = 5;
    const search = ref('');
    const isCreateModalVisible = ref(false);
    const confirmModalVisible = ref(false);
    const productToDelete = ref<number | null>(null);

    const fields = [
      {key: 'id', label: 'ID'},
      {key: 'name', label: 'Nome'},
      {key: 'price', label: 'Preço'},
      {key: 'expiration_date', label: 'Data de Validade'},
      {key: 'actions', label: 'Ações', sortable: false},
    ];

    const {addToast} = useToastService();

    const fetchProducts = async (resetPage = false) => {
      try {
        const page = resetPage ? 1 : currentPage.value;
        const response = await ProductService.getProducts(page, search.value);

        products.value = response.data.data;
        totalItems.value = response.data.total;
        lastPage.value = response.data.last_page;
      } catch (error) {
        console.error(error);
        addToast({
          title: 'Erro!',
          message: 'Não foi possível carregar os produtos.',
          variant: 'danger',
        });
      }
    };

    const openCreateModal = () => {
      modalRef.value?.openModalForCreate();
    };

    const openEditModal = (product: Product) => {
      modalRef.value?.openModalForEdit(product);
    };

    const closeCreateModal = () => {
      isCreateModalVisible.value = false;
    };

    const confirmDelete = (id: number) => {
      productToDelete.value = id;
      confirmModalVisible.value = true;
    };

    const handleDelete = async () => {
      if (productToDelete.value === null) return;
      try {
        await ProductService.deleteProduct(productToDelete.value);
        addToast({
          title: 'Sucesso',
          message: 'Produto excluído com sucesso.',
          variant: 'success',
        });
        await fetchProducts();
        confirmModalVisible.value = false;
      } catch (error) {
        console.error(error);
        addToast({
          title: 'Erro',
          message: 'Erro ao excluir produto.',
          variant: 'danger',
        });
        confirmModalVisible.value = false;
      }
    };

    const cancelDelete = () => {
      confirmModalVisible.value = false;
    };

    fetchProducts();

    return {
      modalRef,
      products,
      totalItems,
      currentPage,
      itemsPerPage,
      search,
      isCreateModalVisible,
      confirmModalVisible,
      fields,
      fetchProducts,
      openCreateModal,
      openEditModal,
      closeCreateModal,
      confirmDelete,
      handleDelete,
      cancelDelete,
    };
  },
});
</script>

<style scoped>
.product-list-container {
  min-height: 100vh;
  background-color: #f9f9f9;
  padding-top: 20px;
}

.b-card-header h5 {
  margin: 0;
}

.b-button i {
  margin-right: 5px;
}
</style>
