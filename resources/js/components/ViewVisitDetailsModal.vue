<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold text-gray-800">Detalles de la Visita</h2>
          <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
        </div>
  
        <div v-if="visitDetails" class="space-y-3 text-gray-700">
          <p><strong>ID:</strong> {{ visitDetails.id }}</p>
          <p><strong>Nombre:</strong> {{ visitDetails.name }}</p>
          <p><strong>Email:</strong> {{ visitDetails.email }}</p>
          <p><strong>Latitud:</strong> {{ visitDetails.latitude }}</p>
          <p><strong>Longitud:</strong> {{ visitDetails.longitude }}</p>
          <p><strong>Creado en:</strong> {{ new Date(visitDetails.created_at).toLocaleString() }}</p>
          <p><strong>Actualizado en:</strong> {{ new Date(visitDetails.updated_at).toLocaleString() }}</p>
        </div>
        <div v-else class="text-center text-gray-600">
          No hay detalles de visita disponibles.
        </div>
  
        <div class="mt-6 text-right">
          <button @click="$emit('close')" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from 'vue';
  
  // Define las props que este componente espera recibir
  const props = defineProps({
    show: Boolean, // Controla la visibilidad del modal
    visitDetails: Object // El objeto de la visita con sus detalles
  });
  
  // Define los eventos que este componente puede emitir
  const emit = defineEmits(['close']);
  </script>
  
  <style scoped>
  /* Estilos adicionales si fueran necesarios */
  </style>
  