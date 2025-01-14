import api from './api';

class CategoryService {
  private baseUri = '/category/';

  createCategory(category: { name: string }) {
    return api.post(this.baseUri, category);
  }

  getCategories(page?: number, search?: string) {
    return api.get(this.baseUri, {
      params: {
        page: page ?? 1,
        search: search ?? '',
      },
    });
  }

  getCategoryById(id: string | number) {
    return api.get(`${this.baseUri}${id}/`);
  }

  updateCategory(id: string | number, category: { name: string }) {
    return api.put(`${this.baseUri}${id}/`, category);
  }

  deleteCategory(id: string | number) {
    return api.delete(`${this.baseUri}${id}/`);
  }
}

export default new CategoryService();
