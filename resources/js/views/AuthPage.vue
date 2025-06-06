<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold">
            {{ authMode === 'login' ? 'Iniciar Sesión' : 'Registrarse' }}
          </h2>
          <!-- No hay botón de cierre aquí, ya que este componente ahora actúa como una página completa gestionada por el router -->
        </div>
  
        <form @submit.prevent="authMode === 'login' ? handleLogin() : handleRegister()" class="space-y-4">
          <div v-if="authMode === 'register'" class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">Nombre:</label>
            <input
              v-model="authForm.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
          </div>
  
          <div class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">Email:</label>
            <input
              v-model="authForm.email"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
          </div>
  
          <div class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">Contraseña:</label>
            <input
              v-model="authForm.password"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
          </div>
  
          <div v-if="authMode === 'register'" class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">Confirmar Contraseña:</label>
            <input
              v-model="authForm.password_confirmation"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
          </div>
  
          <button
            type="submit"
            class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors"
          >
            {{ authMode === 'login' ? 'Ingresar' : 'Registrarse' }}
          </button>
  
          <p
            @click="authMode = authMode === 'login' ? 'register' : 'login'"
            class="text-center text-blue-600 hover:text-blue-800 cursor-pointer underline"
          >
            {{ authMode === 'login'
               ? '¿No tienes cuenta? Regístrate aquí'
               : '¿Ya tienes cuenta? Inicia sesión aquí' }}
          </p>
        </form>
  
        <p v-if="authError" class="mt-4 text-center text-red-600">{{ authError }}</p>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive } from 'vue'; // Importa ref y reactive para la Composition API
  import axios from 'axios';
  import { useRouter } from 'vue-router'; // Importa useRouter para la navegación
  
  // --- Estados Reactivos ---
  const authMode = ref('login'); // Controla si es login o registro
  const authForm = reactive({ // Usamos reactive para objetos complejos
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  });
  const authError = ref(''); // Para mostrar errores de autenticación
  
  // --- Instancia del Router ---
  const router = useRouter();
  
  // --- Funciones de Manejo ---
  
  /**
   * Maneja el proceso de inicio de sesión.
   * Envía credenciales al backend y redirige si es exitoso.
   */
  const handleLogin = async () => {
    try {
      const response = await axios.post('/api/login', {
        email: authForm.email,
        password: authForm.password
      });
      localStorage.setItem('auth_token', response.data.access_token);
      // Redirige al mapa después del login exitoso
      router.push({ name: 'Mapa' }); // Usamos la instancia 'router'
    } catch (error) {
      authError.value = error.response?.data?.message || 'Credenciales incorrectas';
    }
  };
  
  /**
   * Maneja el proceso de registro de usuario.
   * Valida contraseñas, envía datos al backend y luego intenta iniciar sesión.
   */
  const handleRegister = async () => {
    if (authForm.password !== authForm.password_confirmation) {
      authError.value = 'Las contraseñas no coinciden';
      return;
    }
    try {
      await axios.post('/api/register', authForm);
      // Intentar login después del registro para obtener el token y redirigir
      await handleLogin();
    } catch (error) {
      authError.value = error.response?.data?.message || 'Error en el registro';
    }
  };
  
  /**
   * Resetea los campos del formulario de autenticación.
   */
  const resetAuthForm = () => {
    authForm.name = '';
    authForm.email = '';
    authForm.password = '';
    authForm.password_confirmation = '';
    authError.value = ''; // También limpia el error
  };
  </script>
  
  <style scoped>
  /* No se necesitan estilos adicionales ya que está diseñado para ocupar toda la pantalla */
  </style>
  