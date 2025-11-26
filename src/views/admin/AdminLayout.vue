<script setup>
import Header from "../../components/HeaderComponent.vue"
import { useAuth } from "../../composables/useAuth"
import { useRouter } from "vue-router"
import { onMounted } from "vue"

const { isAdmin, loading } = useAuth()
const router = useRouter()

onMounted(() => {
  // Redirigir si no es admin
  if (!loading.value && !isAdmin()) {
    router.push("/")
  }
})
</script>

<template>
  <div class="admin-layout">
    <Header />
    
    <div v-if="loading" class="loading">Cargando...</div>
    
    <div v-else-if="!isAdmin()" class="unauthorized">
      <h2>Acceso no autorizado</h2>
      <p>No tienes permisos para acceder al panel de administración</p>
    </div>
    
    <div v-else class="admin-container">
      <aside class="admin-sidebar">
        <h2>Panel Admin</h2>
        <nav>
          <router-link to="/admin/categories" class="nav-item">
            <span>📁</span> Categorías
          </router-link>
          <router-link to="/admin/vehicles" class="nav-item">
            <span>🚗</span> Vehículos
          </router-link>
          <router-link to="/admin/races" class="nav-item">
            <span>🏁</span> Carreras
          </router-link>
          <router-link to="/admin/positions" class="nav-item">
            <span>🏆</span> Posiciones
          </router-link>
          <router-link to="/admin/users" class="nav-item">
            <span>👥</span> Usuarios
          </router-link>
        </nav>
      </aside>
      
      <main class="admin-content">
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<style scoped>
.admin-layout {
  min-height: 100vh;
  background-color: #323232;
}

.loading, .unauthorized {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
  color: white;
}

.unauthorized h2 {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.admin-container {
  display: flex;
  min-height: calc(100vh - 50px);
}

.admin-sidebar {
  width: 250px;
  background-color: #1E1E1E;
  padding: 2rem 1rem;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.3);
}

.admin-sidebar h2 {
  color: white;
  font-size: 1.5rem;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #ff00dd;
}

.admin-sidebar nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem;
  color: white;
  text-decoration: none;
  border-radius: 5px;
  transition: all 0.3s ease;
}

.nav-item:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.nav-item.router-link-active {
  background-color: #ff00dd;
}

.nav-item span {
  font-size: 1.2rem;
}

.admin-content {
  flex: 1;
  padding: 2rem;
  color: white;
  overflow-y: auto;
}

@media(max-width: 768px) {
  .admin-container {
    flex-direction: column;
  }
  
  .admin-sidebar {
    width: 100%;
  }
  
  .admin-sidebar nav {
    flex-direction: row;
    overflow-x: auto;
  }
}
</style>

