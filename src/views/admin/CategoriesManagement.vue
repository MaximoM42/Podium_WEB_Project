<script setup>
import { ref, onMounted } from "vue"
import AdminLayout from "./AdminLayout.vue"
import { categoryService } from "../../services/categoryService"
import { useAuth } from "../../composables/useAuth"

const { getFirebaseUid } = useAuth()
const categories = ref([])
const loading = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const currentCategory = ref({
  id: '',
  name: '',
  description: '',
  image_url: ''
})

const loadCategories = async () => {
  loading.value = true
  try {
    const response = await categoryService.getAll()
    categories.value = response.categories
  } catch (error) {
    console.error("Error al cargar categorías:", error)
    alert("Error al cargar las categorías")
  } finally {
    loading.value = false
  }
}

const openCreateModal = () => {
  isEditing.value = false
  currentCategory.value = { id: '', name: '', description: '', image_url: '' }
  showModal.value = true
}

const openEditModal = (category) => {
  isEditing.value = true
  currentCategory.value = { ...category }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  currentCategory.value = { id: '', name: '', description: '', image_url: '' }
}

const saveCategory = async () => {
  const firebaseUid = getFirebaseUid()
  if (!firebaseUid) {
    alert("Debes estar autenticado")
    return
  }

  try {
    if (isEditing.value) {
      await categoryService.update(currentCategory.value.id, currentCategory.value, firebaseUid)
      alert("Categoría actualizada correctamente")
    } else {
      await categoryService.create(currentCategory.value, firebaseUid)
      alert("Categoría creada correctamente")
    }
    closeModal()
    loadCategories()
  } catch (error) {
    console.error("Error al guardar categoría:", error)
    alert("Error al guardar la categoría: " + (error.message || "Error desconocido"))
  }
}

const deleteCategory = async (id) => {
  if (!confirm("¿Estás seguro de eliminar esta categoría?")) return

  const firebaseUid = getFirebaseUid()
  if (!firebaseUid) {
    alert("Debes estar autenticado")
    return
  }

  try {
    await categoryService.delete(id, firebaseUid)
    alert("Categoría eliminada correctamente")
    loadCategories()
  } catch (error) {
    console.error("Error al eliminar categoría:", error)
    alert("Error al eliminar la categoría")
  }
}

onMounted(() => {
  loadCategories()
})
</script>

<template>
  <AdminLayout>
    <div class="management-container">
      <div class="header">
        <h1>Gestión de Categorías</h1>
        <button @click="openCreateModal" class="btn-primary">+ Nueva Categoría</button>
      </div>

      <div v-if="loading" class="loading">Cargando...</div>

      <div v-else class="table-container">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Descripción</th>
              <th>Imagen</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="category in categories" :key="category.id">
              <td>{{ category.id }}</td>
              <td>{{ category.name }}</td>
              <td>{{ category.description }}</td>
              <td><img v-if="category.image_url" :src="category.image_url" alt="" class="table-img"></td>
              <td class="actions">
                <button @click="openEditModal(category)" class="btn-edit">Editar</button>
                <button @click="deleteCategory(category.id)" class="btn-delete">Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal -->
      <div v-if="showModal" class="modal-overlay" @click="closeModal">
        <div class="modal" @click.stop>
          <h2>{{ isEditing ? 'Editar' : 'Nueva' }} Categoría</h2>
          <form @submit.prevent="saveCategory">
            <div class="form-group">
              <label>ID:</label>
              <input v-model="currentCategory.id" :disabled="isEditing" required>
            </div>
            <div class="form-group">
              <label>Nombre:</label>
              <input v-model="currentCategory.name" required>
            </div>
            <div class="form-group">
              <label>Descripción:</label>
              <textarea v-model="currentCategory.description"></textarea>
            </div>
            <div class="form-group">
              <label>URL Imagen:</label>
              <input v-model="currentCategory.image_url">
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

<style scoped>
.management-container {
  max-width: 1200px;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.header h1 {
  font-size: 2rem;
  color: white;
}

.table-container {
  background-color: rgba(255, 255, 255, 0.05);
  border-radius: 8px;
  padding: 1rem;
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background-color: rgba(255, 255, 255, 0.1);
}

th, td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

th {
  font-weight: 600;
  color: #ff00dd;
}

.table-img {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.btn-primary, .btn-edit, .btn-delete, .btn-secondary {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.btn-primary {
  background-color: #ff00dd;
  color: white;
}

.btn-primary:hover {
  background-color: #cc00b0;
}

.btn-edit {
  background-color: #548aba;
  color: white;
}

.btn-edit:hover {
  background-color: #3a6a8a;
}

.btn-delete {
  background-color: #c32495;
  color: white;
}

.btn-delete:hover {
  background-color: #901a6d;
}

.btn-secondary {
  background-color: #666;
  color: white;
}

.btn-secondary:hover {
  background-color: #888;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal {
  background-color: #2a2a2a;
  padding: 2rem;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
}

.modal h2 {
  margin-bottom: 1.5rem;
  color: white;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: white;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 4px;
  background-color: rgba(255, 255, 255, 0.1);
  color: white;
}

.form-group textarea {
  min-height: 80px;
  resize: vertical;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: white;
}
</style>

