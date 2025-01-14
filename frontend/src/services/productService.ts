import api from './api';
import CategoryService from './categoryService';

class ProductService {
    private baseUri = '/product/';

    createProduct(formData: FormData) {
        return api.post(this.baseUri, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    }

    getProducts(page?: number, search?: string) {
        return api.get(this.baseUri, {
            params: {
                page: page ?? 1,
                search: search ?? '',
            },
        });
    }

    getProductById(id: string | number) {
        return api.get(`${this.baseUri}${id}/`);
    }

    updateProduct (id: number, formData: FormData) {
        return api.put(`${this.baseUri}${id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    }

    deleteProduct(id: string | number) {
        return api.delete(`${this.baseUri}${id}/`);
    }

    async getCategories() {
        try {
            const response = await CategoryService.getCategories();
            return response.data.data;
        } catch (error) {
            console.error("Erro ao obter categorias:", error);
            throw error;
        }
    }
}

export default new ProductService();
