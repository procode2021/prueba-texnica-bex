<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">

    <nav class="relative flex flex-col sm:flex-row items-center justify-between px-4 sm:px-6 py-3
                bg-gradient-to-r from-blue-700 to-indigo-800 text-white shadow-lg z-20">

      <div class="flex items-center w-full sm:w-auto justify-between mb-3 sm:mb-0">
        <div class="flex items-center">
          <svg class="w-8 h-8 mr-3 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm4 0a1 1 0 100-2 1 1 0 000 2zm3-1a1 1 0 10-2 0v2H8V8a1 1 0 10-2 0v3a1 1 0 001 1h6a1 1 0 001-1V8z" clip-rule="evenodd" />
          </svg>
          <h1 class="text-2xl font-extrabold tracking-tight whitespace-nowrap">Visitas Mapa</h1>
        </div>
        <button @click="toggleMobileMenu" class="sm:hidden text-white focus:outline-none">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
          </svg>
        </button>
      </div>

      <div :class="{'hidden': !isMobileMenuOpen, 'flex': isMobileMenuOpen}"
           class="w-full sm:w-auto flex-col sm:flex-row sm:flex items-center space-y-3 sm:space-y-0 sm:space-x-4 mt-3 sm:mt-0">

        <template v-if="isAuthenticated">
          <router-link
            :to="{ name: 'Mapa' }"
            class="px-4 py-2 rounded-full font-medium transition-all duration-300 w-full sm:w-auto text-center
                   text-blue-100 hover:bg-white hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
            active-class="bg-blue-600 text-white shadow-md"
            @click="isMobileMenuOpen = false"
          >
            Mapa
          </router-link>
          <router-link
            :to="{ name: 'VisitasTabla' }"
            class="px-4 py-2 rounded-full font-medium transition-all duration-300 w-full sm:w-auto text-center
                   text-blue-100 hover:bg-white hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
            active-class="bg-blue-600 text-white shadow-md"
            @click="isMobileMenuOpen = false"
          >
            Tabla de Visitas
          </router-link>

          <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-3
                      bg-blue-600 px-4 py-2 rounded-full shadow-inner w-full sm:w-auto">
            <span class="text-sm font-semibold whitespace-nowrap">Bienvenido, {{ user.name }}</span>
            <button
              @click="logoutAndCloseMenu"
              class="px-3 py-1 bg-red-500 hover:bg-red-600 rounded-full text-xs font-semibold
                     transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-300 w-full sm:w-auto"
            >
              Cerrar Sesión
            </button>
          </div>
        </template>

        <template v-else>
          <router-link
            :to="{ name: 'Auth' }"
            class="px-6 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-md w-full sm:w-auto text-center
                   transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-300"
            @click="isMobileMenuOpen = false"
          >
            Iniciar Sesión / Registrarse
          </router-link>
        </template>
      </div>
    </nav>

    <main class="flex-grow">
      <router-view />
    </main>
  </div>
</template>

<script>
import axios from 'axios';
import { ref, watchEffect } from 'vue';
import { useRouter } from 'vue-router';

export default {
  name: 'App',
  setup() {
    const isAuthenticated = ref(false);
    const user = ref({});
    const router = useRouter();
    const isMobileMenuOpen = ref(false); // Nuevo estado para controlar el menú móvil

    const checkAuthStatus = async () => {
      try {
        const token = localStorage.getItem('auth_token');
        if (token) {
          const response = await axios.get('/api/user', {
            headers: {
              'Authorization': `Bearer ${token}`
            }
          });
          isAuthenticated.value = true;
          user.value = response.data;
        } else {
          isAuthenticated.value = false;
          user.value = {};
        }
      } catch (error) {
        localStorage.removeItem('auth_token');
        isAuthenticated.value = false;
        user.value = {};
      }
    };

    const logout = async () => {
      try {
        await axios.post('/api/logout', {}, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
          }
        });
        localStorage.removeItem('auth_token');
        isAuthenticated.value = false;
        user.value = {};
        router.push({ name: 'Auth' });
      } catch (error) {
        console.error('Error al cerrar sesión:', error);
        localStorage.removeItem('auth_token');
        isAuthenticated.value = false;
        user.value = {};
        router.push({ name: 'Auth' });
      }
    };

    const toggleMobileMenu = () => {
      isMobileMenuOpen.value = !isMobileMenuOpen.value;
    };

    const logoutAndCloseMenu = async () => {
      await logout();
      isMobileMenuOpen.value = false;
    };

    watchEffect(() => {
      checkAuthStatus();
    });

    return {
      isAuthenticated,
      user,
      isMobileMenuOpen, 
      toggleMobileMenu, 
      logout, 
      logoutAndCloseMenu
    };
  }
};
</script>

<style scoped>

</style>