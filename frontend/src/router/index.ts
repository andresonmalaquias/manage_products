import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import LoginView from '@/views/LoginView.vue';
import ProductView from "@/views/ProductView.vue";
import CategoryView from "@/views/CategoryView.vue";

const routes: Array<RouteRecordRaw> = [
  { path: '/:pathMatch(.*)*', redirect: '/login', meta: { hideNavbar: true }, },
  { path: '/login', component: LoginView },
  { path: '/category', component: CategoryView, meta: { requiresAuth: true }, name: 'category' },
  { path: '/product', component: ProductView, meta: { requiresAuth: true }, name: 'product' },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

function isAuthenticated() {
  return !!localStorage.getItem('access_token');
}

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !isAuthenticated()) {
    next('/');
  } else {
    next();
  }
});

export default router;
