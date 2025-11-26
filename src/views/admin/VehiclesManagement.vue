<script setup>
import { ref, onMounted } from "vue"
import AdminLayout from "./AdminLayout.vue"
import { vehicleService } from "../../services/vehicleService"
import { categoryService } from "../../services/categoryService"
import { useAuth } from "../../composables/useAuth"

const { getFirebaseUid } = useAuth()
const vehicles = ref([])
const categories = ref([])
const loading = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const currentVehicle = ref({
  id: null,
  name: '',
  brand: '',
  model: '',
  year: null,
  category_id: ''
})

const loadVehicles = async () => {
  loading.value = true
  try {
    const response = await vehicleService.getAll()
    vehicles.value = response.vehicles
  } catch (error) {
    console.error("Error al cargar vehículos:", error)
    alert("Error al cargar los vehículos")
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
  currentVehicle.value = { id: null, name: '', brand: '', model: '', year: null, category_id: '' }
  showModal.value = true
}

const openEditModal = (vehicle) => {
  isEditing.value = true
  currentVehicle.value = { ...vehicle }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveVehicle = async () => {
  const firebaseUid = getFirebaseUid()
  if (!firebaseUid) {
    alert("Debes estar autenticado")
    return
  }

  try {
    if (isEditing.value) {
      await vehicleService.update(currentVehicle.value.id, currentVehicle.value, firebaseUid)
      alert("Vehículo actualizado correctamente")
    } else {
      await vehicleService.create(currentVehicle.value, firebaseUid)
      alert("Vehículo creado correctamente")
    }
    closeModal()
    loadVehicles()
  } catch (error) {
    console.error("Error al guardar vehículo:", error)
    alert("Error al guardar el vehículo")
  }
}

const deleteVehicle = async (id) => {
  if (!confirm("¿Estás seguro de eliminar este vehículo?")) return

  const firebaseUid = getFirebaseUid()
  try {
    await vehicleService.delete(id, firebaseUid)
    alert("Vehículo eliminado correctamente")
    loadVehicles()
  } catch (error) {
    console.error("Error al eliminar vehículo:", error)
    alert("Error al eliminar el vehículo")
  }
}

onMounted(() => {
  loadVehicles()
  loadCategories()
})
</script>

<template>
  <AdminLayout>
    <div class="management-container">
      <div class="header">
        <h1>Gestión de Vehículos</h1>
        <button @click="openCreateModal" class="btn-primary">+ Nuevo Vehículo</button>
      </div>

      <div v-if="loading" class="loading">Cargando...</div>

      <div v-else class="table-container">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Marca</th>
              <th>Modelo</th>
              <th>Año</th>
              <th>Categoría</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="vehicle in vehicles" :key="vehicle.id">
              <td>{{ vehicle.id }}</td>
              <td>{{ vehicle.name }}</td>
              <td>{{ vehicle.brand }}</td>
              <td>{{ vehicle.model }}</td>
              <td>{{ vehicle.year }}</td>
              <td>{{ vehicle.category_id }}</td>
              <td class="actions">
                <button @click="openEditModal(vehicle)" class="btn-edit">Editar</button>
                <button @click="deleteVehicle(vehicle.id)" class="btn-delete">Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="modal-overlay" @click="closeModal">
        <div class="modal" @click.stop>
          <h2>{{ isEditing ? 'Editar' : 'Nuevo' }} Vehículo</h2>
          <form @submit.prevent="saveVehicle">
            <div class="form-group">
              <label>Nombre:</label>
              <input v-model="currentVehicle.name" required>
            </div>
            <div class="form-group">
              <label>Marca:</label>
              <input v-model="currentVehicle.brand">
            </div>
            <div class="form-group">
              <label>Modelo:</label>
              <input v-model="currentVehicle.model">
            </div>
            <div class="form-group">
              <label>Año:</label>
              <input v-model="currentVehicle.year" type="number">
            </div>
            <div class="form-group">
              <label>Categoría:</label>
              <select v-model="currentVehicle.category_id">
                <option value="">Sin categoría</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
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

