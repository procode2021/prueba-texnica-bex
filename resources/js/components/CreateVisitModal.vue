<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold">Crear Nueva Vista</h2>
          <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <form @submit.prevent="createVisit" class="space-y-3">
          <input
            type="text"
            v-model="newVisit.name"
            placeholder="Nombre de la vista"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
          <input
            type="email"
            v-model="newVisit.email"
            placeholder="Email"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
          <input
            type="text"
            v-model="newVisit.latitude"
            placeholder="Latitud"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
          <input
            type="text"
            v-model="newVisit.longitude"
            placeholder="Longitud"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
          <button
            type="submit"
            class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors"
          >
            Guardar
          </button>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import axios from 'axios';
  
  // --- Props ---
  // Define las props que este componente espera recibir
  const props = defineProps({
    show: Boolean
  });
  
  // --- Emits ---
  // Define los eventos que este componente puede emitir
  const emit = defineEmits(['close', 'visitCreated', 'message']); // Añadido 'message' para alertas
  
  // --- Estados Reactivos ---
  // Usamos ref para almacenar los datos de la nueva visita
  const newVisit = ref({
    name: '',
    email: '',
    latitude: '',
    longitude: ''
  });
  

  /**
   * Crea una nueva visita enviando los datos a la API.
   * Emite un evento 'visitCreated' si la creación es exitosa.
   */
  const createVisit = async () => {
    try {
      const response = await axios.post('/api/visits', newVisit.value, { // Accede al valor de la ref
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
      });
      emit('visitCreated', response.data); // Emite el evento con la nueva visita
      emit('close'); // Cierra el modal
      resetForm(); // Resetea el formulario después de una creación exitosa
      emit('message', { text: 'Visita creada exitosamente.', type: 'success' }); // Mensaje de éxito
    } catch (error) {
      console.error('Error al crear visita:', error);
      // Emite un mensaje de error si la creación falla
      emit('message', { text: 'Hubo un error al crear la visita.', type: 'error' });
    }
  };
  
  /**
   * Resetea los campos del formulario de la nueva visita.
   */
  const resetForm = () => {
    newVisit.value = { // Resetea el valor de la ref
      name: '',
      email: '',
      latitude: '',
      longitude: ''
    };
  };
  </script>
  
  <style scoped>
  
  </style>
  