<template>
  <ConfirmCancelModal
      :isVisible="confirmModalVisible"
      @update:isVisible="confirmModalVisible = $event"
      @ok="handleDelete"
      @cancel="cancelDelete"
      :title="'Confirmar Exclusão'"
      :message="'Você tem certeza que deseja excluir esta categoria?'"
  />

  <div class="category-list-container">
    <b-container fluid>
      <b-row class="align-items-center my-3">
        <b-col md="6" sm="12" class="mb-2 mb-md-0">
          <b-input-group>
            <b-form-input
                v-model="search"
                placeholder="Buscar categorias..."
                debounce="300"
            />
            <b-input-group-append>
              <b-button variant="outline-primary" @click="fetchCategories(true)">
                Buscar
              </b-button>
            </b-input-group-append>
          </b-input-group>
        </b-col>
        <b-col md="6" sm="12" class="text-md-end text-sm-start">
          <b-button variant="success" @click="openCreateModal">
            <i class="bi bi-plus-circle"></i> Adicionar Categoria
          </b-button>
        </b-col>
      </b-row>

      <b-card class="shadow-sm mt-3">
        <b-card-header>
          <h5 class="mb-0">Lista de Categorias</h5>
        </b-card-header>
        <b-card-body>
          <b-table
              :items="categories"
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
              <b-button size="sm" variant="danger" @click="confirmDeleteCategory(row.item.id)">
                <i class="bi bi-trash"></i> Excluir
              </b-button>
            </template>
          </b-table>

          <b-pagination
              v-model="currentPage"
              :total-rows="totalItems"
              :per-page="itemsPerPage"
              align="center"
              @click="fetchCategories()"
          />
        </b-card-body>
      </b-card>
    </b-container>

    <CreateCategoryModal
        ref="modalRef"
        v-model:isVisible="isCreateModalVisible"
        @create="fetchCategories"
        @cancel="closeCreateModal"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, type Ref } from 'vue';
import { useToastService } from '@/services/toastService';
import CreateCategoryModal from '@/components/CreateCategoryModal.vue';
import ConfirmCancelModal from '@/components/ConfirmCancelModal.vue';
import CategoryService from '@/services/categoryService';

type CreateCategoryModalType = {
  openModalForCreate: () => void;
  openModalForEdit: (category: { id: number; name: string }) => void;
};

export default defineComponent({
  name: 'CategoryView',
  components: {
    CreateCategoryModal,
    ConfirmCancelModal,
  },
  setup() {
    const modalRef: Ref<CreateCategoryModalType | null> = ref(null);
    const categories = ref([]);
    const totalItems = ref(0);
    const currentPage = ref(1);
    const lastPage = ref(1);
    const itemsPerPage = 5;
    const search = ref('');
    const isCreateModalVisible = ref(false);
    const confirmModalVisible = ref(false);
    const categoryToDelete = ref<number | null>(null);


    const fields = [
      { key: 'id', label: 'ID' },
      { key: 'name', label: 'Nome' },
      { key: 'actions', label: 'Ações' },
    ];

    const { addToast } = useToastService();

    const fetchCategories = async (resetPage = false) => {
      try {
        const page = resetPage ? 1 : currentPage.value;

        const response = await CategoryService.getCategories(page, search.value);
        const data = response.data;

        categories.value = data.data;
        totalItems.value = data.total;
        lastPage.value = data.last_page;
      } catch (error) {
        console.error(error);
        addToast({
          title: 'Erro',
          message: 'Erro ao carregar categorias. Tente novamente.',
          variant: 'danger',
        });
      }
    };

    const openCreateModal = () => {
      modalRef.value?.openModalForCreate();
    };

    const openEditModal = (category: { id: number; name: string }) => {
      modalRef.value?.openModalForEdit(category);
    };

    const closeCreateModal = () => {
      isCreateModalVisible.value = false;
    };

    const confirmDeleteCategory = (id: number) => {
      categoryToDelete.value = id;
      confirmModalVisible.value = true;
    };

    const handleDelete = async () => {
      if (categoryToDelete.value === null) return;
      try {
        await CategoryService.deleteCategory(categoryToDelete.value);
        addToast({
          title: 'Sucesso',
          message: 'Categoria excluída com sucesso.',
          variant: 'success',
        });
        await fetchCategories();
        confirmModalVisible.value = false;
      } catch (error) {
        console.error(error);
        addToast({
          title: 'Erro',
          message: 'Erro ao excluir categoria.',
          variant: 'danger',
        });
        confirmModalVisible.value = false;
      }
    };

    const cancelDelete = () => {
      confirmModalVisible.value = false;
    };

    fetchCategories();

    return {
      modalRef,
      categories,
      totalItems,
      currentPage,
      lastPage,
      itemsPerPage,
      search,
      fields,
      isCreateModalVisible,
      confirmModalVisible,
      openCreateModal,
      openEditModal,
      closeCreateModal,
      fetchCategories,
      confirmDeleteCategory,
      handleDelete,
      cancelDelete,
    };
  },
});
</script>

<style scoped>
.category-list-container {
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
