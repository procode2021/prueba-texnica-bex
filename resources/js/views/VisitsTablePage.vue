<template>
  <div class="p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Gestión de Visitas</h1>

    <div v-if="loading" class="text-center text-gray-600">Cargando visitas...</div>
    <div v-else-if="error" class="text-center text-red-600">Error al cargar visitas: {{ error }}</div>
    <div v-else-if="visits.length === 0" class="text-center text-gray-600">
      No hay visitas registradas.
      <router-link :to="{ name: 'Mapa' }" class="text-blue-600 hover:underline">
        Crea una nueva vista en el mapa.
      </router-link>
    </div>
    <div v-else class="overflow-x-auto bg-white shadow-md rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Latitud</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Longitud</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="visit in visits" :key="visit.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ visit.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ visit.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ visit.latitude }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ visit.longitude }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
              <button @click="viewVisitDetails(visit)" class="text-blue-600 hover:text-blue-900">Ver Detalles</button>
              <button @click="editVisit(visit)" class="text-indigo-600 hover:text-indigo-900">Editar</button>
              <button @click="showConfirmDelete(visit.id)" class="text-red-600 hover:text-red-900">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Edit Visit Modal -->
    <EditVisitModal
      :show="showEditModal"
      :visit="currentVisit"
      @close="showEditModal = false"
      @visitUpdated="handleVisitUpdated"
      @message="showMessage"
    />

    <!-- View Visit Details Modal (Nuevo) -->
    <ViewVisitDetailsModal
      :show="showDetailsModal"
      :visitDetails="selectedVisitDetails"
      @close="showDetailsModal = false"
    />

    <!-- Custom Confirmation Dialog for Delete -->
    <div v-if="confirmingDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-sm text-center">
        <p class="text-lg font-semibold mb-4">¿Estás seguro de que quieres eliminar esta visita?</p>
        <div class="flex justify-center space-x-4">
          <button @click="confirmDeleteAction" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors">
            Sí, Eliminar
          </button>
          <button @click="cancelDelete" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md transition-colors">
            Cancelar
          </button>
        </div>
      </div>
    </div>

    <!-- Custom Message Display for Alerts -->
    <div v-if="message" class="fixed bottom-4 right-4 z-50 p-4 rounded-md shadow-lg transition-all duration-300 ease-in-out"
         :class="{ 'bg-green-500 text-white': messageType === 'success', 'bg-red-500 text-white': messageType === 'error', 'bg-yellow-500 text-gray-800': messageType === 'warning' }">
      <p>{{ message }}</p>
      <button @click="message = ''; messageType = '';" class="ml-4 text-white font-bold"
              :class="{ 'text-white': messageType === 'success' || messageType === 'error', 'text-gray-800': messageType === 'warning' }">&times;</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import EditVisitModal from '../components/EditVisitModal.vue';
import ViewVisitDetailsModal from '../components/ViewVisitDetailsModal.vue'; // Importar el nuevo modal

// --- Estados Reactivos ---
const visits = ref([]);
const loading = ref(true);
const error = ref(null);
const showEditModal = ref(false);
const currentVisit = ref(null);
const confirmingDelete = ref(false);
const visitToDeleteId = ref(null);
const message = ref('');
const messageType = ref(''); // Nuevo: para el tipo de mensaje (success, error, warning)
const showDetailsModal = ref(false); // Nuevo: para controlar la visibilidad del modal de detalles
const selectedVisitDetails = ref(null); // Nuevo: para almacenar los detalles de la visita seleccionada

// --- Instancia del Router ---
const router = useRouter();

// --- Funciones de Manejo ---

/**
 * Obtiene las visitas desde la API.
 */
const fetchVisits = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get('/api/visits', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      }
    });
    visits.value = response.data;
  } catch (err) {
    console.error('Error al obtener visitas para la tabla:', err);
    error.value = err.response?.data?.message || 'Error al cargar las visitas.';
    showMessage(error.value, 'error'); // Mostrar error en la notificación
    if (err.response && err.response.status === 401) {
      router.push({ name: 'Auth' });
    }
  } finally {
    loading.value = false;
  }
};

/**
 * Abre el modal de edición y carga los datos de la visita seleccionada.
 * @param {Object} visit - La visita a editar.
 */
const editVisit = (visit) => {
  currentVisit.value = { ...visit };
  showEditModal.value = true;
};

/**
 * Maneja el evento 'visitUpdated' desde EditVisitModal.
 * Actualiza la visita en la lista local.
 * @param {Object} updatedVisit - La visita con los datos actualizados.
 */
const handleVisitUpdated = (updatedVisit) => {
  const index = visits.value.findIndex(v => v.id === updatedVisit.id);
  if (index !== -1) {
    visits.value[index] = updatedVisit;
  }
  showEditModal.value = false;
};

/**
 * Muestra el diálogo de confirmación para eliminar una visita.
 * @param {number} id - El ID de la visita a eliminar.
 */
const showConfirmDelete = (id) => {
  visitToDeleteId.value = id;
  confirmingDelete.value = true;
};

/**
 * Confirma y procede con la eliminación de la visita.
 */
const confirmDeleteAction = async () => {
  if (visitToDeleteId.value) {
    await deleteVisit(visitToDeleteId.value);
  }
  confirmingDelete.value = false;
  visitToDeleteId.value = null;
};

/**
 * Cancela la operación de eliminación.
 */
const cancelDelete = () => {
  confirmingDelete.value = false;
  visitToDeleteId.value = null;
};

/**
 * Elimina una visita de la API y actualiza la lista local.
 * @param {number} id - El ID de la visita a eliminar.
 */
const deleteVisit = async (id) => {
  try {
    await axios.delete(`/api/visits/${id}`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
      }
    });
    visits.value = visits.value.filter(visit => visit.id !== id);
    showMessage('Visita eliminada exitosamente.', 'success');
  } catch (err) {
    console.error('Error al eliminar la visita:', err);
    showMessage('Hubo un error al eliminar la visita.', 'error');
  }
};

/**
 * Muestra un mensaje personalizado.
 * @param {string} text - El texto del mensaje.
 * @param {string} type - El tipo de mensaje ('success', 'error', 'warning').
 */
const showMessage = (data) => {
  message.value = data.text;
  messageType.value = data.type;
  setTimeout(() => {
    message.value = '';
    messageType.value = '';
  }, 3000);
};

/**
 * Muestra el modal de detalles de una visita.
 * @param {Object} visit - La visita cuyos detalles se quieren ver.
 */
const viewVisitDetails = (visit) => {
  selectedVisitDetails.value = { ...visit }; // Copia el objeto para el modal
  showDetailsModal.value = true;
};

// --- Hooks del ciclo de vida ---

// Se ejecuta cuando el componente ha sido montado en el DOM
onMounted(() => {
  fetchVisits();
});
</script>

<style scoped>

</style>
