<script setup>
import { ref, onMounted } from "vue"
import AdminLayout from "./AdminLayout.vue"
import { raceService } from "../../services/raceService"
import { categoryService } from "../../services/categoryService"
import { useAuth } from "../../composables/useAuth"

const { getFirebaseUid } = useAuth()
const races = ref([])
const categories = ref([])
const loading = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const currentRace = ref({
  id: null,
  category_id: '',
  name: '',
  location: '',
  date: '',
  status: 'scheduled'
})

const loadRaces = async () => {
  loading.value = true
  try {
    const response = await raceService.getAll()
    races.value = response.races
  } catch (error) {
    console.error("Error al cargar carreras:", error)
    alert("Error al cargar las carreras")
  } finally {
    loading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await categoryService.getAll()
    categories.value = response.categories
  } catch (error) {
    console.error("Error al cargar categorías:", error)
  }
}

const openCreateModal = () => {
  isEditing.value = false
  currentRace.value = { id: null, category_id: '', name: '', location: '', date: '', status: 'scheduled' }
  showModal.value = true
}

const openEditModal = (race) => {
  isEditing.value = true
  currentRace.value = { ...race }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveRace = async () => {
  const firebaseUid = getFirebaseUid()
  if (!firebaseUid) {
    alert("Debes estar autenticado")
    return
  }

  try {
    if (isEditing.value) {
      await raceService.update(currentRace.value.id, currentRace.value, firebaseUid)
      alert("Carrera actualizada correctamente")
    } else {
      await raceService.create(currentRace.value, firebaseUid)
      alert("Carrera creada correctamente")
    }
    closeModal()
    loadRaces()
  } catch (error) {
    console.error("Error al guardar carrera:", error)
    alert("Error al guardar la carrera")
  }
}

const deleteRace = async (id) => {
  if (!confirm("¿Estás seguro de eliminar esta carrera?")) return

  const firebaseUid = getFirebaseUid()
  try {
    await raceService.delete(id, firebaseUid)
    alert("Carrera eliminada correctamente")
    loadRaces()
  } catch (error) {
    console.error("Error al eliminar carrera:", error)
    alert("Error al eliminar la carrera")
  }
}

onMounted(() => {
  loadRaces()
  loadCategories()
})
</script>

<template>
  <AdminLayout>
    <div class="management-container">
      <div class="header">
        <h1>Gestión de Carreras</h1>
        <button @click="openCreateModal" class="btn-primary">+ Nueva Carrera</button>
      </div>

      <div v-if="loading" class="loading">Cargando...</div>

      <div v-else class="table-container">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Categoría</th>
              <th>Ubicación</th>
              <th>Fecha</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="race in races" :key="race.id">
              <td>{{ race.id }}</td>
              <td>{{ race.name }}</td>
              <td>{{ race.category_id }}</td>
              <td>{{ race.location }}</td>
              <td>{{ race.date }}</td>
              <td>
                <span :class="'status-' + race.status">{{ race.status }}</span>
              </td>
              <td class="actions">
                <button @click="openEditModal(race)" class="btn-edit">Editar</button>
                <button @click="deleteRace(race.id)" class="btn-delete">Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="modal-overlay" @click="closeModal">
        <div class="modal" @click.stop>
          <h2>{{ isEditing ? 'Editar' : 'Nueva' }} Carrera</h2>
          <form @submit.prevent="saveRace">
            <div class="form-group">
              <label>Nombre:</label>
              <input v-model="currentRace.name" required>
            </div>
            <div class="form-group">
              <label>Categoría:</label>
              <select v-model="currentRace.category_id" required>
                <option value="">Seleccionar categoría</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label>Ubicación:</label>
              <input v-model="currentRace.location">
            </div>
            <div class="form-group">
              <label>Fecha:</label>
              <input v-model="currentRace.date" type="date" required>
            </div>
            <div class="form-group">
              <label>Estado:</label>
              <select v-model="currentRace.status">
                <option value="scheduled">Programada</option>
                <option value="ongoing">En curso</option>
                <option value="finished">Finalizada</option>
              </select>
            </div>
            <div class="form-actions">
              <button type="button" @click="closeModal" class="btn-secondary">Cancelar</button>
              <button type="submit" class="btn-primary">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped src="./admin-styles.css"></style>
<style scoped>
.status-scheduled {
  color: #548aba;
  font-weight: 600;
}
.status-ongoing {
  color: #ff9800;
  font-weight: 600;
}
.status-finished {
  color: #4caf50;
  font-weight: 600;
}
</style>

