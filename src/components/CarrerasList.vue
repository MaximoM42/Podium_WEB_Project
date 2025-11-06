<template>
  <div class="carreras-list-container">
    <!-- Header con filtros -->
    <div class="container-fluid py-4">
      <div class="row mb-4">
        <div class="col-md-12">
          <h2 class="mb-4">
            <i class="fas fa-flag-checkered"></i> Carreras Disponibles
          </h2>
          
          <!-- Filtros -->
          <div class="card shadow-sm mb-4">
            <div class="card-body">
              <form @submit.prevent="cargarCarreras" class="row align-items-end">
                <!-- Estado -->
                <div class="col-md-3 mb-2">
                  <label class="form-label">Estado</label>
                  <select v-model="filtros.estado" class="form-control">
                    <option value="">Todas</option>
                    <option value="inscripciones_abiertas">Inscripciones Abiertas</option>
                    <option value="programada">Programadas</option>
                    <option value="en_curso">En Curso</option>
                    <option value="finalizada">Finalizadas</option>
                  </select>
                </div>

                <!-- Categoría -->
                <div class="col-md-3 mb-2">
                  <label class="form-label">Categoría</label>
                  <select v-model="filtros.categoria" class="form-control">
                    <option value="">Todas</option>
                    <option 
                      v-for="cat in categorias" 
                      :key="cat.id_categoria"
                      :value="cat.id_categoria"
                    >
                      {{ cat.nombre }}
                    </option>
                  </select>
                </div>

                <!-- Fecha Desde -->
                <div class="col-md-3 mb-2">
                  <label class="form-label">Desde</label>
                  <input 
                    v-model="filtros.fecha_desde" 
                    type="date" 
                    class="form-control"
                  >
                </div>

                <!-- Fecha Hasta -->
                <div class="col-md-2 mb-2">
                  <label class="form-label">Hasta</label>
                  <input 
                    v-model="filtros.fecha_hasta" 
                    type="date" 
                    class="form-control"
                  >
                </div>

                <!-- Botón Filtrar -->
                <div class="col-md-1 mb-2">
                  <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-filter"></i>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="cargando" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Cargando...</span>
        </div>
        <p class="mt-3">Cargando carreras...</p>
      </div>

      <!-- Grid de Carreras -->
      <div v-else-if="carreras.length > 0" class="row">
        <div 
          v-for="carrera in carreras" 
          :key="carrera.id_carrera"
          class="col-md-6 col-lg-4 mb-4"
        >
          <div class="card h-100 shadow-sm carrera-card">
            <!-- Imagen -->
            <img 
              :src="carrera.imagen_url || '/img/default-race.jpg'" 
              :alt="carrera.nombre_carrera"
              class="card-img-top"
              style="height: 200px; object-fit: cover;"
            >
            
            <!-- Badge de Estado -->
            <div class="position-absolute" style="top: 10px; right: 10px;">
              <span 
                class="badge"
                :class="getBadgeClass(carrera.estado)"
              >
                {{ getEstadoTexto(carrera.estado) }}
              </span>
            </div>

            <div class="card-body d-flex flex-column">
              <!-- Título -->
              <h5 class="card-title">{{ carrera.nombre_carrera }}</h5>
              
              <!-- Categoría -->
              <p class="text-muted mb-2">
                <small>
                  <i class="fas fa-tag"></i> {{ carrera.categoria }}
                </small>
              </p>

              <!-- Descripción -->
              <p class="card-text text-truncate-3">
                {{ carrera.descripcion }}
              </p>

              <!-- Información -->
              <ul class="list-unstyled mb-3">
                <li class="mb-1">
                  <i class="fas fa-map-marker-alt text-danger"></i>
                  <small>{{ carrera.circuito }}, {{ carrera.ciudad }}, {{ carrera.pais }}</small>
                </li>
                <li class="mb-1">
                  <i class="fas fa-calendar-alt text-primary"></i>
                  <small>{{ formatearFecha(carrera.fecha_inicio) }}</small>
                </li>
                <li class="mb-1">
                  <i class="fas fa-dollar-sign text-success"></i>
                  <small><strong>${{ formatearPrecio(carrera.precio_inscripcion) }}</strong></small>
                </li>
                <li class="mb-1">
                  <i class="fas fa-users text-info"></i>
                  <small>
                    {{ carrera.inscriptos }}/{{ carrera.cupo_maximo }} inscriptos
                  </small>
                  <span 
                    v-if="carrera.tiene_cupos" 
                    class="badge badge-success ml-2"
                  >
                    {{ carrera.cupos_disponibles }} disponibles
                  </span>
                  <span 
                    v-else 
                    class="badge badge-danger ml-2"
                  >
                    Agotado
                  </span>
                </li>
              </ul>

              <!-- Botón Ver Detalle -->
              <button 
                @click="verDetalle(carrera.id_carrera)"
                class="btn btn-outline-primary btn-block mt-auto"
              >
                <i class="fas fa-info-circle"></i> Ver Detalle
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Sin Resultados -->
      <div v-else class="text-center py-5">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h4 class="text-muted">No se encontraron carreras</h4>
        <p class="text-muted">Intenta cambiar los filtros de búsqueda</p>
      </div>
    </div>
  </div>
</template>

<script>
import carrerasService from '@/services/carrerasService';
import categoriasService from '@/services/categoriasService';

export default {
  name: 'CarrerasList',
  
  data() {
    return {
      carreras: [],
      categorias: [],
      filtros: {
        estado: 'inscripciones_abiertas',
        categoria: '',
        fecha_desde: '',
        fecha_hasta: ''
      },
      cargando: false
    };
  },

  mounted() {
    this.cargarCategorias();
    this.cargarCarreras();
  },

  methods: {
    async cargarCategorias() {
      try {
        const response = await categoriasService.listar();
        if (response.success) {
          this.categorias = response.data.categorias;
        }
      } catch (error) {
        console.error('Error al cargar categorías:', error);
      }
    },

    async cargarCarreras() {
      this.cargando = true;
      try {
        const filtrosLimpios = {};
        
        // Solo enviar filtros con valores
        if (this.filtros.estado) filtrosLimpios.estado = this.filtros.estado;
        if (this.filtros.categoria) filtrosLimpios.categoria = this.filtros.categoria;
        if (this.filtros.fecha_desde) filtrosLimpios.fecha_desde = this.filtros.fecha_desde;
        if (this.filtros.fecha_hasta) filtrosLimpios.fecha_hasta = this.filtros.fecha_hasta;

        const response = await carrerasService.listar(filtrosLimpios);
        
        if (response.success) {
          this.carreras = response.data.carreras;
        }
      } catch (error) {
        console.error('Error al cargar carreras:', error);
        this.$toast?.error('Error al cargar carreras');
      } finally {
        this.cargando = false;
      }
    },

    verDetalle(id) {
      this.$router.push(`/carreras/${id}`);
    },

    formatearFecha(fecha) {
      const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      };
      return new Date(fecha).toLocaleDateString('es-AR', options);
    },

    formatearPrecio(precio) {
      return Number(precio).toLocaleString('es-AR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    },

    getBadgeClass(estado) {
      const classes = {
        'inscripciones_abiertas': 'badge-success',
        'programada': 'badge-info',
        'en_curso': 'badge-warning',
        'finalizada': 'badge-secondary',
        'cancelada': 'badge-danger'
      };
      return classes[estado] || 'badge-secondary';
    },

    getEstadoTexto(estado) {
      const textos = {
        'inscripciones_abiertas': 'Inscripciones Abiertas',
        'programada': 'Programada',
        'en_curso': 'En Curso',
        'finalizada': 'Finalizada',
        'cancelada': 'Cancelada'
      };
      return textos[estado] || estado;
    }
  }
};
</script>

<style scoped>
.carrera-card {
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer;
}

.carrera-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.text-truncate-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  min-height: 4.5em;
}
</style>
