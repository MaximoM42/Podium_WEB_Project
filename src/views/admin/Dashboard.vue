<script setup>
import AdminLayout from "./AdminLayout.vue"
import { ref, onMounted } from "vue"
import { categoryService } from "../../services/categoryService"
import { vehicleService } from "../../services/vehicleService"
import { raceService } from "../../services/raceService"

const stats = ref({
  categories: 0,
  vehicles: 0,
  races: 0,
  loading: true
})

const loadStats = async () => {
  try {
    const [categoriesRes, vehiclesRes, racesRes] = await Promise.all([
      categoryService.getAll(),
      vehicleService.getAll(),
      raceService.getAll()
    ])
    
    stats.value = {
      categories: categoriesRes.categories?.length || 0,
      vehicles: vehiclesRes.vehicles?.length || 0,
      races: racesRes.races?.length || 0,
      loading: false
    }
  } catch (error) {
    console.error("Error al cargar estadísticas:", error)
    stats.value.loading = false
  }
}

onMounted(() => {
  loadStats()
})
</script>

<template>
  <AdminLayout>
    <div class="dashboard">
      <h1>Panel de Administración</h1>
      <p class="subtitle">Bienvenido al sistema de gestión de Podium</p>

      <div v-if="stats.loading" class="loading">Cargando estadísticas...</div>

      <div v-else class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">📁</div>
          <div class="stat-info">
            <h3>Categorías</h3>
            <p class="stat-number">{{ stats.categories }}</p>
          </div>
          <router-link to="/admin/categories" class="stat-link">Gestionar →</router-link>
        </div>

        <div class="stat-card">
          <div class="stat-icon">🚗</div>
          <div class="stat-info">
            <h3>Vehículos</h3>
            <p class="stat-number">{{ stats.vehicles }}</p>
          </div>
          <router-link to="/admin/vehicles" class="stat-link">Gestionar →</router-link>
        </div>

        <div class="stat-card">
          <div class="stat-icon">🏁</div>
          <div class="stat-info">
            <h3>Carreras</h3>
            <p class="stat-number">{{ stats.races }}</p>
          </div>
          <router-link to="/admin/races" class="stat-link">Gestionar →</router-link>
        </div>
      </div>

      <div class="quick-links">
        <h2>Acciones Rápidas</h2>
        <div class="links-grid">
          <router-link to="/admin/categories" class="quick-link-card">
            <span class="icon">📁</span>
            <span>Gestionar Categorías</span>
          </router-link>
          <router-link to="/admin/vehicles" class="quick-link-card">
            <span class="icon">🚗</span>
            <span>Gestionar Vehículos</span>
          </router-link>
          <router-link to="/admin/races" class="quick-link-card">
            <span class="icon">🏁</span>
            <span>Gestionar Carreras</span>
          </router-link>
          <router-link to="/categories" class="quick-link-card">
            <span class="icon">👁️</span>
            <span>Ver Sitio Público</span>
          </router-link>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style scoped>
.dashboard {
  max-width: 1200px;
  margin: 0 auto;
}

h1 {
  font-size: 2.5rem;
  color: white;
  margin-bottom: 0.5rem;
}

.subtitle {
  color: #aaa;
  margin-bottom: 3rem;
  font-size: 1.1rem;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: white;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.stat-card {
  background: linear-gradient(135deg, rgba(255, 0, 221, 0.1), rgba(84, 138, 186, 0.1));
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  padding: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(255, 0, 221, 0.3);
}

.stat-icon {
  font-size: 3rem;
}

.stat-info h3 {
  color: white;
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
}

.stat-number {
  color: #ff00dd;
  font-size: 2.5rem;
  font-weight: 700;
}

.stat-link {
  color: #548aba;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.stat-link:hover {
  color: #ff00dd;
}

.quick-links {
  margin-top: 3rem;
}

.quick-links h2 {
  color: white;
  font-size: 1.8rem;
  margin-bottom: 1.5rem;
}

.links-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.quick-link-card {
  background-color: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  color: white;
  text-decoration: none;
  transition: all 0.3s ease;
}

.quick-link-card:hover {
  background-color: rgba(255, 0, 221, 0.1);
  border-color: #ff00dd;
  transform: translateX(5px);
}

.quick-link-card .icon {
  font-size: 2rem;
}

@media (max-width: 768px) {
  h1 {
    font-size: 2rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .links-grid {
    grid-template-columns: 1fr;
  }
}
</style>

