<template>
  <div class="inscripcion-form-container">
    <div class="container py-4">
      <!-- Breadcrumb -->
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <router-link to="/carreras">Carreras</router-link>
          </li>
          <li class="breadcrumb-item">
            <router-link :to="`/carreras/${idCarrera}`">Detalle</router-link>
          </li>
          <li class="breadcrumb-item active">Inscripción</li>
        </ol>
      </nav>

      <h2 class="mb-4">
        <i class="fas fa-edit"></i> Inscripción a Carrera
      </h2>

      <!-- Loading -->
      <div v-if="cargando" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
        <p class="mt-3">Cargando información...</p>
      </div>

      <!-- Formulario -->
      <div v-else-if="carrera" class="row">
        <!-- Formulario Principal -->
        <div class="col-lg-8">
          <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">Datos de Inscripción</h5>
            </div>
            <div class="card-body">
              <form @submit.prevent="confirmarInscripcion">
                <!-- Información del Piloto (readonly) -->
                <h6 class="border-bottom pb-2 mb-3">Piloto</h6>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label>Nombre</label>
                    <input 
                      type="text" 
                      class="form-control" 
                      :value="piloto.nombre"
                      readonly
                    >
                  </div>
                  <div class="form-group col-md-6">
                    <label>Apellido</label>
                    <input 
                      type="text" 
                      class="form-control" 
                      :value="piloto.apellido"
                      readonly
                    >
                  </div>
                </div>

                <!-- Selección de Vehículo -->
                <h6 class="border-bottom pb-2 mb-3 mt-4">Vehículo</h6>
                <div class="form-group">
                  <label>Selecciona tu vehículo *</label>
                  <select 
                    v-model="formulario.id_vehicle" 
                    class="form-control"
                    required
                  >
                    <option value="">-- Seleccione un vehículo --</option>
                    <option 
                      v-for="vehiculo in vehiculos" 
                      :key="vehiculo.id_vehicle"
                      :value="vehiculo.id_vehicle"
                    >
                      {{ vehiculo.marca }} {{ vehiculo.nombre }} - {{ vehiculo.tipo }}
                    </option>
                  </select>
                  <small class="form-text text-muted">
                    El vehículo debe cumplir con las especificaciones de la categoría
                  </small>
                </div>

                <!-- Método de Pago -->
                <h6 class="border-bottom pb-2 mb-3 mt-4">Pago</h6>
                <div class="form-group">
                  <label>Método de Pago *</label>
                  <select 
                    v-model="formulario.metodo_pago" 
                    class="form-control"
                    required
                  >
                    <option value="tarjeta_credito">Tarjeta de Crédito</option>
                    <option value="tarjeta_debito">Tarjeta de Débito</option>
                    <option value="transferencia">Transferencia Bancaria</option>
                    <option value="efectivo">Efectivo</option>
                  </select>
                </div>

                <!-- Entrega del Vehículo -->
                <h6 class="border-bottom pb-2 mb-3 mt-4">Entrega del Vehículo</h6>
                <div class="form-group">
                  <div class="custom-control custom-checkbox">
                    <input 
                      type="checkbox" 
                      class="custom-control-input" 
                      id="requiereEntrega"
                      v-model="formulario.requiere_entrega"
                    >
                    <label class="custom-control-label" for="requiereEntrega">
                      Requiero que entreguen mi vehículo en el circuito
                    </label>
                  </div>
                  <small class="form-text text-muted">
                    Si activas esta opción, nuestro equipo se encargará de transportar tu vehículo al circuito
                  </small>
                </div>

                <!-- Dirección de Retiro (si requiere entrega) -->
                <div v-if="formulario.requiere_entrega" class="border p-3 rounded mt-3">
                  <h6 class="mb-3">Dirección de Retiro del Vehículo</h6>
                  
                  <div class="form-group">
                    <label>Dirección *</label>
                    <input 
                      v-model="formulario.direccion_entrega" 
                      type="text" 
                      class="form-control"
                      placeholder="Ej: Av. Siempreviva 742"
                      :required="formulario.requiere_entrega"
                    >
                  </div>

                  <div class="form-row">
                    <div class="form-group col-md-8">
                      <label>Ciudad *</label>
                      <input 
                        v-model="formulario.ciudad_entrega" 
                        type="text" 
                        class="form-control"
                        placeholder="Ej: Buenos Aires"
                        :required="formulario.requiere_entrega"
                      >
                    </div>
                    <div class="form-group col-md-4">
                      <label>Código Postal *</label>
                      <input 
                        v-model="formulario.codigo_postal_entrega" 
                        type="text" 
                        class="form-control"
                        placeholder="Ej: 1234"
                        :required="formulario.requiere_entrega"
                      >
                    </div>
                  </div>
                </div>

                <!-- Términos y Condiciones -->
                <div class="form-group mt-4">
                  <div class="custom-control custom-checkbox">
                    <input 
                      type="checkbox" 
                      class="custom-control-input" 
                      id="aceptoTerminos"
                      v-model="aceptoTerminos"
                      required
                    >
                    <label class="custom-control-label" for="aceptoTerminos">
                      Acepto los <a href="#" @click.prevent="mostrarTerminos">términos y condiciones</a> *
                    </label>
                  </div>
                </div>

                <!-- Botones -->
                <div class="d-flex justify-content-between mt-4">
                  <button 
                    type="button" 
                    @click="$router.go(-1)"
                    class="btn btn-outline-secondary"
                  >
                    <i class="fas fa-times"></i> Cancelar
                  </button>
                  <button 
                    type="submit" 
                    class="btn btn-success btn-lg"
                    :disabled="procesando || !aceptoTerminos"
                  >
                    <span v-if="procesando">
                      <span class="spinner-border spinner-border-sm mr-2"></span>
                      Procesando...
                    </span>
                    <span v-else>
                      <i class="fas fa-check-circle"></i> Confirmar Inscripción
                    </span>
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Resumen -->
        <div class="col-lg-4">
          <div class="card shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header bg-info text-white">
              <h5 class="mb-0">Resumen</h5>
            </div>
            <div class="card-body">
              <h6 class="mb-3">{{ carrera.nombre }}</h6>
              
              <ul class="list-unstyled">
                <li class="mb-2">
                  <small class="text-muted">Categoría:</small><br>
                  <strong>{{ carrera.categoria }}</strong>
                </li>
                <li class="mb-2">
                  <small class="text-muted">Circuito:</small><br>
                  <strong>{{ carrera.circuito }}</strong>
                </li>
                <li class="mb-2">
                  <small class="text-muted">Ubicación:</small><br>
                  {{ carrera.ciudad }}, {{ carrera.pais }}
                </li>
                <li class="mb-2">
                  <small class="text-muted">Fecha:</small><br>
                  {{ formatearFecha(carrera.fecha_inicio) }}
                </li>
                <li class="mb-2">
                  <small class="text-muted">Cupos disponibles:</small><br>
                  <span 
                    class="badge"
                    :class="carrera.cupos_disponibles > 5 ? 'badge-success' : 'badge-warning'"
                  >
                    {{ carrera.cupos_disponibles }} de {{ carrera.cupo_maximo }}
                  </span>
                </li>
              </ul>

              <hr>

              <div class="text-center">
                <h6 class="text-muted mb-2">Precio de Inscripción</h6>
                <h3 class="text-success mb-0">
                  ${{ formatearPrecio(carrera.precio_inscripcion) }}
                </h3>
              </div>

              <hr>

              <div class="alert alert-info mb-0">
                <small>
                  <i class="fas fa-info-circle"></i>
                  Tu número de competidor será asignado automáticamente al confirmar la inscripción.
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Confirmación -->
    <div 
      class="modal fade" 
      id="modalConfirmacion" 
      tabindex="-1" 
      ref="modalConfirmacion"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title">
              <i class="fas fa-check-circle"></i> ¡Inscripción Exitosa!
            </h5>
          </div>
          <div class="modal-body text-center py-4">
            <i class="fas fa-trophy fa-4x text-warning mb-3"></i>
            <h4>¡Te has inscrito exitosamente!</h4>
            <p class="mb-3">{{ carrera?.nombre }}</p>
            
            <div class="alert alert-success">
              <h5 class="mb-0">Tu número de competidor es:</h5>
              <h2 class="text-success mb-0">
                #{{ numeroCompetidor }}
              </h2>
            </div>

            <p class="text-muted">
              <small>
                Recibirás un email con los detalles de tu inscripción
              </small>
            </p>
          </div>
          <div class="modal-footer">
            <button 
              type="button" 
              class="btn btn-primary"
              @click="irAMisInscripciones"
            >
              Ver Mis Inscripciones
            </button>
            <button 
              type="button" 
              class="btn btn-secondary"
              @click="cerrarModal"
            >
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import carrerasService from '@/services/carrerasService';
import inscripcionesService from '@/services/inscripcionesService';

export default {
  name: 'InscripcionForm',

  data() {
    return {
      idCarrera: this.$route.params.id,
      carrera: null,
      piloto: {
        id_piloto: 1, // TODO: Obtener del usuario autenticado
        nombre: 'Juan',
        apellido: 'Pérez'
      },
      vehiculos: [
        // TODO: Obtener vehículos del piloto desde el backend
        { id_vehicle: 1, marca: 'Ferrari', nombre: 'F8 Tributo', tipo: 'Deportivo' },
        { id_vehicle: 2, marca: 'Lamborghini', nombre: 'Huracán', tipo: 'Superdeportivo' },
        { id_vehicle: 3, marca: 'Porsche', nombre: '911 GT3', tipo: 'Deportivo' }
      ],
      formulario: {
        id_piloto: 1, // TODO: Del usuario autenticado
        id_carrera: this.$route.params.id,
        id_vehicle: '',
        metodo_pago: 'tarjeta_credito',
        requiere_entrega: false,
        direccion_entrega: '',
        ciudad_entrega: '',
        codigo_postal_entrega: ''
      },
      aceptoTerminos: false,
      cargando: false,
      procesando: false,
      numeroCompetidor: null
    };
  },

  mounted() {
    this.cargarCarrera();
  },

  methods: {
    async cargarCarrera() {
      this.cargando = true;
      try {
        const response = await carrerasService.obtenerDetalle(this.idCarrera);
        if (response.success) {
          this.carrera = response.data.carrera;
          
          // Verificar que acepta inscripciones
          if (!this.carrera.acepta_inscripciones) {
            this.$toast?.error('Esta carrera no acepta inscripciones');
            this.$router.push(`/carreras/${this.idCarrera}`);
          }
        }
      } catch (error) {
        console.error('Error al cargar carrera:', error);
        this.$toast?.error('Error al cargar información de la carrera');
      } finally {
        this.cargando = false;
      }
    },

    async confirmarInscripcion() {
      // Validaciones
      if (!this.formulario.id_vehicle) {
        this.$toast?.error('Debes seleccionar un vehículo');
        return;
      }

      if (!this.aceptoTerminos) {
        this.$toast?.error('Debes aceptar los términos y condiciones');
        return;
      }

      this.procesando = true;

      try {
        const response = await inscripcionesService.crear(this.formulario);

        if (response.success) {
          this.numeroCompetidor = response.data.numero_competidor;
          
          // Mostrar modal de éxito
          this.$nextTick(() => {
            window.jQuery('#modalConfirmacion').modal('show');
          });
        }
      } catch (error) {
        console.error('Error en inscripción:', error);
        const mensaje = error.message || 'Error al procesar la inscripción';
        this.$toast?.error(mensaje);
      } finally {
        this.procesando = false;
      }
    },

    mostrarTerminos() {
      alert('Términos y Condiciones:\n\n1. El piloto debe cumplir con todas las normativas de seguridad.\n2. El vehículo debe estar en perfectas condiciones.\n3. Los pagos son no reembolsables excepto en caso de cancelación de la carrera.\n4. El piloto acepta todos los riesgos asociados a la competencia.');
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

    irAMisInscripciones() {
      this.$router.push('/mis-inscripciones');
    },

    cerrarModal() {
      window.jQuery('#modalConfirmacion').modal('hide');
      this.$router.push('/carreras');
    }
  }
};
</script>

<style scoped>
.sticky-top {
  position: sticky;
}
</style>
