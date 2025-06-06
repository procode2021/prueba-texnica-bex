<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold">Cargar Visitas desde Excel</h2>
          <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <input
          type="file"
          @change="handleFileUpload"
          accept=".xlsx, .xls, .csv"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
        <button
          @click="importVisits"
          :disabled="!excelFile"
          class="w-full mt-4 py-2 px-4 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Importar
        </button>
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
  const emit = defineEmits(['close', 'visitsImported', 'message']); // Añadido 'message' para alertas
  
  // --- Estados Reactivos ---
  const excelFile = ref(null); // Usamos ref para almacenar el archivo seleccionado
  
  // --- Funciones de Manejo ---
  
  /**
   * Maneja la selección de un archivo de Excel.
   * Almacena el archivo en la variable reactiva excelFile.
   * @param {Event} event - El evento de cambio del input de archivo.
   */
  const handleFileUpload = (event) => {
    excelFile.value = event.target.files[0];
  };
  
  /**
   * Importa las visitas desde el archivo de Excel seleccionado.
   * Realiza una solicitud POST a la API con el archivo.
   */
  const importVisits = async () => {
    if (!excelFile.value) {
      // Si no hay archivo seleccionado, emite un mensaje de advertencia
      emit('message', { text: 'Por favor selecciona un archivo primero.', type: 'warning' });
      return;
    }
  
    try {
      const formData = new FormData();
      formData.append('file', excelFile.value); // Accede al valor de la ref
  
      const response = await axios.post('/api/visits/import', formData, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`,
          'Content-Type': 'multipart/form-data'
        }
      });
  
      emit('visitsImported', response.data.data); // Emite las visitas importadas
      emit('close'); // Cierra el modal
      excelFile.value = null; // Limpia el archivo seleccionado para el próximo uso
      emit('message', { text: 'Visitas importadas exitosamente.', type: 'success' }); // Mensaje de éxito
    } catch (error) {
      console.error('Error al importar visitas:', error);
      // Emite un mensaje de error si la importación falla
      emit('message', { text: 'Error al importar el archivo. Asegúrate de que el formato sea correcto.', type: 'error' });
    }
  };
  </script>
  
  <style scoped>
  /* No se necesitan estilos scoped adicionales, Tailwind lo maneja */
  </style>
  