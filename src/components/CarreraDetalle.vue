<template>
  <div class="carrera-detalle-container">
    <!-- Loading -->
    <div v-if="cargando" class="container py-5">
      <div class="text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Cargando...</span>
        </div>
        <p class="mt-3">Cargando información de la carrera...</p>
      </div>
    </div>

    <!-- Contenido -->
    <div v-else-if="carrera" class="container py-4">
      <!-- Botón Volver -->
      <button @click="$router.go(-1)" class="btn btn-outline-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Volver
      </button>

      <!-- Header con imagen -->
      <div class="card shadow-sm mb-4">
        <img 
          :src="carrera.imagen_url || '/img/default-race.jpg'" 
          :alt="carrera.nombre"
          class="card-img-top"
          style="max-height: 400px; object-fit: cover;"
        >
        <div class="card-body">
          <div class="row">
            <div class="col-md-8">
              <h2 class="card-title mb-3">{{ carrera.nombre }}</h2>
              <p class="lead">{{ carrera.descripcion }}</p>
              
              <!-- Badges -->
              <div class="mb-3">
                <span 
                  class="badge badge-lg mr-2"
                  :class="getBadgeClass(carrera.estado)"
                >
                  {{ getEstadoTexto(carrera.estado) }}
                </span>
                <span class="badge badge-primary badge-lg">
                  {{ carrera.categoria }}
                </span>
              </div>
            </div>

            <!-- Precio e Inscripción -->
            <div class="col-md-4">
              <div class="card bg-light">
                <div class="card-body text-center">
                  <h5 class="text-muted mb-2">Precio de Inscripción</h5>
                  <h2 class="text-success mb-3">
                    ${{ formatearPrecio(carrera.precio_inscripcion) }}
                  </h2>
                  
                  <!-- Cupos -->
                  <div class="mb-3">
                    <small class="text-muted">Cupos Disponibles</small>
                    <div class="progress mt-2" style="height: 25px;">
                      <div 
                        class="progress-bar"
                        :class="carrera.cupos_disponibles > 5 ? 'bg-success' : 'bg-warning'"
                        role="progressbar" 
                        :style="`width: ${(carrera.inscriptos / carrera.cupo_maximo) * 100}%`"
                        :aria-valuenow="carrera.inscriptos" 
                        aria-valuemin="0" 
                        :aria-valuemax="carrera.cupo_maximo"
                      >
                        {{ carrera.inscriptos }}/{{ carrera.cupo_maximo }}
                      </div>
                    </div>
                    <small>{{ carrera.cupos_disponibles }} cupos disponibles</small>
                  </div>

                  <!-- Botón Inscripción -->
                  <button 
                    v-if="carrera.acepta_inscripciones"
                    @click="irAInscripcion"
                    class="btn btn-success btn-block btn-lg"
                    :disabled="!carrera.tiene_cupos"
                  >
                    <i class="fas fa-check-circle"></i> Inscribirme
                  </button>
                  <button 
                    v-else
                    class="btn btn-secondary btn-block btn-lg"
                    disabled
                  >
                    <i class="fas fa-lock"></i> Inscripciones Cerradas
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Información Detallada -->
      <div class="row mb-4">
        <!-- Información de la Carrera -->
        <div class="col-md-6 mb-3">
          <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">
                <i class="fas fa-info-circle"></i> Información de la Carrera
              </h5>
            </div>
            <div class="card-body">
              <ul class="list-unstyled mb-0">
                <li class="mb-2">
                  <strong><i class="fas fa-calendar-alt text-primary"></i> Fecha y Hora:</strong><br>
                  {{ formatearFecha(carrera.fecha_inicio) }}
                </li>
                <li class="mb-2" v-if="carrera.fecha_fin">
                  <strong><i class="fas fa-calendar-check text-success"></i> Finaliza:</strong><br>
                  {{ formatearFecha(carrera.fecha_fin) }}
                </li>
                <li class="mb-2" v-if="carrera.numero_vueltas">
                  <strong><i class="fas fa-sync text-info"></i> Número de Vueltas:</strong><br>
                  {{ carrera.numero_vueltas }} vueltas
                </li>
                <li class="mb-2" v-if="carrera.distancia_total">
                  <strong><i class="fas fa-road text-warning"></i> Distancia Total:</strong><br>
                  {{ carrera.distancia_total }} km
                </li>
                <li class="mb-2" v-if="carrera.clima">
                  <strong><i class="fas fa-cloud-sun text-secondary"></i> Clima:</strong><br>
                  {{ carrera.clima }}
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Información del Circuito -->
        <div class="col-md-6 mb-3">
          <div class="card shadow-sm h-100">
            <div class="card-header bg-danger text-white">
              <h5 class="mb-0">
                <i class="fas fa-map-marked-alt"></i> Circuito
              </h5>
            </div>
            <div class="card-body">
              <h6>{{ carrera.circuito }}</h6>
              <p class="text-muted mb-3">
                <i class="fas fa-map-marker-alt"></i> 
                {{ carrera.ciudad }}, {{ carrera.pais }}
              </p>
              
              <ul class="list-unstyled mb-0">
                <li class="mb-2" v-if="carrera.longitud_pista">
                  <strong><i class="fas fa-ruler text-primary"></i> Longitud:</strong><br>
                  {{ carrera.longitud_pista }} km
                </li>
                <li class="mb-2" v-if="carrera.numero_curvas">
                  <strong><i class="fas fa-exchange-alt text-info"></i> Curvas:</strong><br>
                  {{ carrera.numero_curvas }} curvas
                </li>
                <li class="mb-2" v-if="carrera.tipo_pista">
                  <strong><i class="fas fa-flag text-success"></i> Tipo:</strong><br>
                  {{ carrera.tipo_pista }}
                </li>
                <li class="mb-2" v-if="carrera.capacidad_espectadores">
                  <strong><i class="fas fa-users text-warning"></i> Capacidad:</strong><br>
                  {{ carrera.capacidad_espectadores.toLocaleString() }} espectadores
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Premios -->
      <div v-if="carrera.premio_primero || carrera.premio_segundo || carrera.premio_tercero" class="card shadow-sm mb-4">
        <div class="card-header bg-warning">
          <h5 class="mb-0">
            <i class="fas fa-trophy"></i> Premios
          </h5>
        </div>
        <div class="card-body">
          <div class="row text-center">
            <div class="col-md-4" v-if="carrera.premio_primero">
              <h3><i class="fas fa-medal text-warning"></i></h3>
              <h6>1º Puesto</h6>
              <h4 class="text-success">${{ formatearPrecio(carrera.premio_primero) }}</h4>
            </div>
            <div class="col-md-4" v-if="carrera.premio_segundo">
              <h3><i class="fas fa-medal text-secondary"></i></h3>
              <h6>2º Puesto</h6>
              <h4 class="text-success">${{ formatearPrecio(carrera.premio_segundo) }}</h4>
            </div>
            <div class="col-md-4" v-if="carrera.premio_tercero">
              <h3><i class="fas fa-medal" style="color: #cd7f32;"></i></h3>
              <h6>3º Puesto</h6>
              <h4 class="text-success">${{ formatearPrecio(carrera.premio_tercero) }}</h4>
            </div>
          </div>
        </div>
      </div>

      <!-- Pilotos Inscritos (si la carrera está en curso o finalizada) -->
      <div v-if="pilotos.length > 0" class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
          <h5 class="mb-0">
            <i class="fas fa-users"></i> Pilotos Inscritos ({{ pilotos.length }})
          </h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Piloto</th>
                  <th>Vehículo</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="piloto in pilotos" :key="piloto.id_piloto">
                  <td><strong>{{ piloto.numero_competidor }}</strong></td>
                  <td>
                    {{ piloto.nombre }} {{ piloto.apellido }}
                    <span v-if="piloto.apodo" class="text-muted">
                      "{{ piloto.apodo }}"
                    </span>
                  </td>
                  <td>
                    <small>{{ piloto.marca }} {{ piloto.vehiculo }}</small>
                  </td>
                  <td>
                    <span 
                      class="badge"
                      :class="piloto.estado_pago === 'completado' ? 'badge-success' : 'badge-warning'"
                    >
                      {{ piloto.estado_pago }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Resultados (si la carrera finalizó) -->
      <div v-if="resultados.length > 0" class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
          <h5 class="mb-0">
            <i class="fas fa-flag-checkered"></i> Resultados Finales
          </h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Pos.</th>
                  <th>#</th>
                  <th>Piloto</th>
                  <th>Vehículo</th>
                  <th>Tiempo</th>
                  <th>Mejor Vuelta</th>
                  <th>Puntos</th>
                </tr>
              </thead>
              <tbody>
                <tr 
                  v-for="resultado in resultados" 
                  :key="resultado.id_resultado"
                  :class="getPosicionClass(resultado.posicion_final)"
                >
                  <td>
                    <strong>{{ resultado.posicion_final }}º</strong>
                    <i v-if="resultado.posicion_final === 1" class="fas fa-trophy text-warning ml-1"></i>
                  </td>
                  <td>{{ resultado.numero_competidor }}</td>
                  <td>
                    {{ resultado.nombre }} {{ resultado.apellido }}
                    <span v-if="resultado.apodo" class="text-muted">
                      <br><small>"{{ resultado.apodo }}"</small>
                    </span>
                  </td>
                  <td><small>{{ resultado.marca }} {{ resultado.vehiculo }}</small></td>
                  <td>{{ resultado.tiempo_total || '-' }}</td>
                  <td>{{ resultado.mejor_vuelta || '-' }}</td>
                  <td><strong>{{ resultado.puntos_obtenidos || 0 }}</strong></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Transmisión (si está disponible) -->
      <div v-if="carrera.transmision_url" class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
          <h5 class="mb-0">
            <i class="fas fa-video"></i> Transmisión en Vivo
          </h5>
        </div>
        <div class="card-body">
          <a :href="carrera.transmision_url" target="_blank" class="btn btn-danger btn-lg">
            <i class="fab fa-youtube"></i> Ver Transmisión
          </a>
        </div>
      </div>
    </div>

    <!-- Error -->
    <div v-else class="container py-5">
      <div class="alert alert-danger text-center">
        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
        <h4>Carrera no encontrada</h4>
        <button @click="$router.push('/carreras')" class="btn btn-primary mt-3">
          Volver a Carreras
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import carrerasService from '@/services/carrerasService';

export default {
  name: 'CarreraDetalle',
  
  data() {
    return {
      carrera: null,
      pilotos: [],
      resultados: [],
      cargando: false
    };
  },

  mounted() {
    this.cargarDetalle();
  },

  methods: {
    async cargarDetalle() {
      this.cargando = true;
      try {
        const id = this.$route.params.id;
        const response = await carrerasService.obtenerDetalle(id);
        
        if (response.success) {
          this.carrera = response.data.carrera;
          this.pilotos = response.data.pilotos || [];
          this.resultados = response.data.resultados || [];
        }
      } catch (error) {
        console.error('Error al cargar detalle:', error);
        this.$toast?.error('Error al cargar la carrera');
      } finally {
        this.cargando = false;
      }
    },

    irAInscripcion() {
      this.$router.push(`/inscripcion/${this.carrera.id_carrera}`);
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
    },

    getPosicionClass(posicion) {
      if (posicion === 1) return 'table-warning';
      if (posicion === 2) return 'table-secondary';
      if (posicion === 3) return 'table-info';
      return '';
    }
  }
};
</script>

<style scoped>
.badge-lg {
  font-size: 1rem;
  padding: 0.5rem 1rem;
}
</style>
