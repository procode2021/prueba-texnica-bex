<template>
    <div class="relative w-full h-[calc(100vh-64px)]">
      <div id="map" class="h-full w-full"></div>
  
      <div class="absolute bottom-4 right-4 z-10 flex space-x-2">
        <button
          @click="showCreateModal = true"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-md transition-colors"
        >
          Crear Vista
        </button>
        <button
          @click="showImportModal = true"
          class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md shadow-md transition-colors"
        >
          Cargar desde Excel
        </button>
      </div>
  
      <!-- Los modales se pasan las props y escuchan los eventos emitidos -->
      <CreateVisitModal
        :show="showCreateModal"
        @close="showCreateModal = false"
        @visitCreated="handleVisitCreated"
      />
  
      <ImportVisitsModal
        :show="showImportModal"
        @close="showImportModal = false"
        @visitsImported="handleVisitsImported"
      />
    </div>
  </template>
  
  <script setup>
  // Importaciones de Vue Composition API
  import { ref, onMounted, onBeforeUnmount } from 'vue';
  // Importaciones de librerías externas
  import 'leaflet/dist/leaflet.css';
  import L from 'leaflet';
  import axios from 'axios';
  import { useRouter } from 'vue-router'; // Hook para acceder al router
  
  // Importación de componentes hijos
  import CreateVisitModal from '../components/CreateVisitModal.vue';
  import ImportVisitsModal from '../components/ImportVisitsModal.vue';
  
  // --- Declaración de estados reactivos ---
  const map = ref(null); // Usamos ref para variables que pueden cambiar su valor directamente
  const markers = ref([]);
  const visits = ref([]);
  const showCreateModal = ref(false);
  const showImportModal = ref(false);
  
  // Obtenemos la instancia del router
  const router = useRouter();
  
  // --- Funciones (equivalentes a los métodos de Options API) ---
  
  /**
   * Inicializa el mapa de Leaflet.
   * Si ya existe una instancia de mapa, la remueve para evitar duplicados.
   */
  const initMap = () => {
    if (map.value) { // Accedemos al valor de la ref con .value
      map.value.remove();
    }
    map.value = L.map('map').setView([20.0, -10.0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map.value);
  };
  
  /**
   * Fetches visits data from the API and updates the map.
   * Obtiene los datos de las visitas desde la API y actualiza el mapa.
   */
  const fetchVisits = async () => {
    try {
      const response = await axios.get('/api/visits', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
      });
      visits.value = response.data; // Actualizamos el valor de la ref 'visits'
      updateMarkers();
    } catch (error) {
      console.error('Error al obtener visitas para el mapa:', error);
      if (error.response && error.response.status === 401) {
        // Si el token expira o es inválido, redirige a la página de autenticación
        router.push({ name: 'Auth' }); // Usamos router.push de la instancia del router
      }
    }
  };
  
  /**
   * Updates the markers on the Leaflet map based on the current visits data.
   * Elimina los marcadores existentes y añade nuevos para cada visita.
   */
  const updateMarkers = () => {
    markers.value.forEach(marker => map.value.removeLayer(marker));
    markers.value = [];
  
    visits.value.forEach(visit => {
      const marker = L.marker([visit.latitude, visit.longitude])
        .addTo(map.value)
        .bindPopup(`<b>${visit.name}</b><br>${visit.email}`);
      markers.value.push(marker);
    });
  
    if (markers.value.length > 0) {
      const group = new L.featureGroup(markers.value);
      map.value.fitBounds(group.getBounds(), { padding: [50, 50] }); // Añade un poco de padding alrededor de los marcadores
    }
  };
  
  /**
   * Handles the 'visitCreated' event from the CreateVisitModal.
   * Añade la nueva visita a la lista y actualiza los marcadores.
   * @param {Object} newVisit - The new visit data.
   */
  const handleVisitCreated = (newVisit) => {
    visits.value.push(newVisit); // Agregamos directamente a la ref del array
    updateMarkers();
  };
  
  /**
   * Handles the 'visitsImported' event from the ImportVisitsModal.
   * Sobrescribe la lista actual de visitas con las importadas y actualiza los marcadores.
   * @param {Array} importedVisits - An array of imported visit data.
   */
  const handleVisitsImported = (importedVisits) => {
    visits.value = importedVisits; // Sobreescribimos el array de ref
    updateMarkers();
  };
  
  // --- Hooks del ciclo de vida (Composition API) ---
  
  // Se ejecuta cuando el componente ha sido montado en el DOM
  onMounted(() => {
    initMap();
    fetchVisits();
  });
  
  // Se ejecuta justo antes de que el componente sea desmontado del DOM
  onBeforeUnmount(() => {
    if (map.value) {
      map.value.remove(); // Limpia el mapa para evitar pérdidas de memoria
    }
  });
  </script>
  
  <style scoped>
  #map {
    z-index: 0; /* Asegura que el mapa esté en el fondo si hay otros elementos flotantes */
  }
  </style>
  