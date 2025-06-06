<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold">Editar Visita</h2>
          <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <form @submit.prevent="updateVisit" class="space-y-3">
          <input
            type="text"
            v-model="editedVisit.name"
            placeholder="Nombre de la vista"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
          <input
            type="email"
            v-model="editedVisit.email"
            placeholder="Email"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
          <input
            type="text"
            v-model="editedVisit.latitude"
            placeholder="Latitud"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
          <input
            type="text"
            v-model="editedVisit.longitude"
            placeholder="Longitud"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
          <button
            type="submit"
            class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors"
          >
            Actualizar
          </button>
        </form>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue'; 
  import axios from 'axios';
  
  // --- Props ---
  // Define las props que este componente espera recibir
  const props = defineProps({
    show: Boolean,
    visit: Object // La visita que se va a editar
  });
  
  // --- Emits ---
  // Define los eventos que este componente puede emitir
  const emit = defineEmits(['close', 'visitUpdated', 'message']); // Añadido 'message' para alertas
  
  // --- Estados Reactivos ---
  // Usamos ref para almacenar una copia mutable de la prop 'visit'
  const editedVisit = ref({});
  
  // --- Observadores ---
  // Observa los cambios en la prop 'visit' para actualizar el formulario interno
  watch(
    () => props.visit, // Función getter para la prop 'visit'
    (newVal) => {
      // Cuando la prop 'visit' cambia, actualizamos la copia interna 'editedVisit'
      // Se usa una copia profunda para que los cambios en el formulario no muten la prop directamente
      editedVisit.value = { ...newVal };
    },
    {
      deep: true, // Observa cambios profundos dentro del objeto 'visit'
      immediate: true // Ejecuta el handler inmediatamente al montar el componente
    }
  );
  
  // --- Funciones de Manejo ---
  
  /**
   * Actualiza la visita haciendo una solicitud PUT a la API.
   * Emite un evento 'visitUpdated' si la actualización es exitosa.
   */
  const updateVisit = async () => {
    try {
      const response = await axios.put(`/api/visits/${editedVisit.value.id}`, editedVisit.value, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
      });
      emit('visitUpdated', response.data); // Emite la visita actualizada al componente padre
      emit('close'); // Cierra el modal
      emit('message', { text: 'Visita actualizada exitosamente.', type: 'success' }); // Mensaje de éxito
    } catch (error) {
      console.error('Error al actualizar visita:', error);
      // Emite un mensaje de error si la actualización falla
      emit('message', { text: 'Hubo un error al actualizar la visita.', type: 'error' });
    }
  };
  </script>
  
  <style scoped>
  /* No se necesitan estilos scoped adicionales, Tailwind lo maneja */
  </style>
  