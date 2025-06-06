// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router';
import MapPage from '../views/MapPage.vue';
import VisitsTablePage from '../views/VisitsTablePage.vue';
import AuthModal from '../views/AuthPage.vue'; // Si decides mantenerlo como una página de ruta inicial para autenticación

const routes = [
  {
    path: '/',
    name: 'Home',
    redirect: '/mapa' // Redirige la ruta raíz a la página del mapa por defecto
  },
  {
    path: '/mapa',
    name: 'Mapa',
    component: MapPage,
    meta: { requiresAuth: true } // Marca esta ruta para requerir autenticación
  },
  {
    path: '/visitas',
    name: 'VisitasTabla',
    component: VisitsTablePage,
    meta: { requiresAuth: true } // Marca esta ruta para requerir autenticación
  },
  {
    path: '/login-register',
    name: 'Auth',
    component: AuthModal // O un componente de página para el login/registro
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Guardia de navegación global para la autenticación
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('auth_token'); // Verifica si hay un token
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);

  if (requiresAuth && !isAuthenticated) {
    next({ name: 'Auth' }); // Redirige a la página de login/registro si requiere autenticación y no está autenticado
  } else if ((to.name === 'Auth' || to.name === 'Home') && isAuthenticated) {
    next({ name: 'Mapa' }); // Si ya está autenticado y trata de ir a login/registro o home, redirige al mapa
  } else {
    next(); // Continúa la navegación
  }
});

export default router;