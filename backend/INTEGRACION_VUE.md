# Integración Vue.js con Backend PODIUM

Esta guía muestra cómo integrar el frontend Vue.js con el backend PHP de PODIUM.

## 📦 Instalación de Dependencias

```bash
# En el directorio del proyecto Vue.js
npm install axios
```

## 🔧 Configuración de Axios

Crear archivo `src/services/api.js`:

```javascript
import axios from "axios";

// URL base del backend
const API_BASE_URL = "http://localhost/Podium_WEB_Project/backend";

// Instancia de Axios configurada
const apiClient = axios.create({
	baseURL: API_BASE_URL,
	withCredentials: true, // Importante para sesiones PHP
	headers: {
		"Content-Type": "application/json",
		Accept: "application/json",
	},
});

// Interceptor para manejar errores globalmente
apiClient.interceptors.response.use(
	(response) => response,
	(error) => {
		if (error.response) {
			// Error con respuesta del servidor
			console.error("Error API:", error.response.data);

			// Redirigir a login si es 401
			if (error.response.status === 401) {
				// router.push('/login'); // Descomentar si usas Vue Router
			}
		}
		return Promise.reject(error);
	}
);

export default apiClient;
```

---

## 📡 Servicios API

### Carreras Service

Crear `src/services/carrerasService.js`:

```javascript
import apiClient from "./api";

export default {
	/**
	 * Obtener lista de carreras
	 * @param {Object} filtros - { estado, categoria, fecha_desde, fecha_hasta }
	 */
	async listar(filtros = {}) {
		try {
			const params = new URLSearchParams();

			if (filtros.estado) params.append("estado", filtros.estado);
			if (filtros.categoria)
				params.append("categoria", filtros.categoria);
			if (filtros.fecha_desde)
				params.append("fecha_desde", filtros.fecha_desde);
			if (filtros.fecha_hasta)
				params.append("fecha_hasta", filtros.fecha_hasta);

			const response = await apiClient.get(
				`/api/carreras/listar.php?${params}`
			);
			return response.data;
		} catch (error) {
			throw error.response?.data || error;
		}
	},

	/**
	 * Obtener detalle de una carrera
	 * @param {Number} id - ID de la carrera
	 */
	async obtenerDetalle(id) {
		try {
			const response = await apiClient.get(
				`/api/carreras/detalle.php?id=${id}`
			);
			return response.data;
		} catch (error) {
			throw error.response?.data || error;
		}
	},

	/**
	 * Obtener carreras con inscripciones abiertas
	 */
	async obtenerAbiertas() {
		return this.listar({ estado: "inscripciones_abiertas" });
	},
};
```

---

### Inscripciones Service

Crear `src/services/inscripcionesService.js`:

```javascript
import apiClient from "./api";

export default {
	/**
	 * Crear nueva inscripción
	 * @param {Object} datos - Datos de la inscripción
	 */
	async crear(datos) {
		try {
			const response = await apiClient.post(
				"/Terminal_carga_productos.php",
				datos
			);
			return response.data;
		} catch (error) {
			throw error.response?.data || error;
		}
	},

	/**
	 * Obtener inscripciones del piloto autenticado
	 * @param {String} firebaseUid - UID de Firebase del piloto
	 */
	async misInscripciones(firebaseUid) {
		try {
			const response = await apiClient.get(
				`/api/inscripciones/mis-inscripciones.php?firebase_uid=${firebaseUid}`
			);
			return response.data;
		} catch (error) {
			throw error.response?.data || error;
		}
	},
};
```

---

### Categorías Service

Crear `src/services/categoriasService.js`:

```javascript
import apiClient from "./api";

export default {
	/**
	 * Obtener todas las categorías
	 */
	async listar() {
		try {
			const response = await apiClient.get("/api/categorias/listar.php");
			return response.data;
		} catch (error) {
			throw error.response?.data || error;
		}
	},
};
```

---

## 🎯 Ejemplos de Uso en Componentes Vue

### Listar Carreras

```vue
<template>
	<div class="carreras-list">
		<h2>Carreras Disponibles</h2>

		<!-- Filtros -->
		<div class="filtros">
			<select v-model="filtroEstado">
				<option value="">Todas</option>
				<option value="inscripciones_abiertas">
					Inscripciones Abiertas
				</option>
				<option value="en_curso">En Curso</option>
				<option value="finalizada">Finalizadas</option>
			</select>

			<button @click="cargarCarreras">Filtrar</button>
		</div>

		<!-- Loading -->
		<div v-if="cargando" class="loading">Cargando...</div>

		<!-- Lista de carreras -->
		<div v-else class="carreras-grid">
			<div
				v-for="carrera in carreras"
				:key="carrera.id_carrera"
				class="carrera-card"
			>
				<img :src="carrera.imagen_url" :alt="carrera.nombre_carrera" />
				<h3>{{ carrera.nombre_carrera }}</h3>
				<p>{{ carrera.descripcion }}</p>
				<div class="info">
					<span>📍 {{ carrera.circuito }}, {{ carrera.pais }}</span>
					<span>📅 {{ formatearFecha(carrera.fecha_inicio) }}</span>
					<span>💰 ${{ carrera.precio_inscripcion }}</span>
					<span>
						👥 {{ carrera.inscriptos }}/{{ carrera.cupo_maximo }}
						<span v-if="carrera.tiene_cupos" class="disponible"
							>✓ Disponible</span
						>
						<span v-else class="agotado">✗ Agotado</span>
					</span>
				</div>
				<button
					@click="verDetalle(carrera.id_carrera)"
					class="btn-primary"
				>
					Ver Detalle
				</button>
			</div>
		</div>

		<!-- Sin resultados -->
		<div v-if="!cargando && carreras.length === 0" class="no-results">
			No se encontraron carreras
		</div>
	</div>
</template>

<script>
import carrerasService from "@/services/carrerasService";

export default {
	name: "CarrerasList",

	data() {
		return {
			carreras: [],
			filtroEstado: "inscripciones_abiertas",
			cargando: false,
		};
	},

	mounted() {
		this.cargarCarreras();
	},

	methods: {
		async cargarCarreras() {
			this.cargando = true;
			try {
				const response = await carrerasService.listar({
					estado: this.filtroEstado,
				});

				if (response.success) {
					this.carreras = response.data.carreras;
				}
			} catch (error) {
				console.error("Error al cargar carreras:", error);
				alert("Error al cargar carreras");
			} finally {
				this.cargando = false;
			}
		},

		formatearFecha(fecha) {
			return new Date(fecha).toLocaleDateString("es-AR", {
				day: "2-digit",
				month: "long",
				year: "numeric",
			});
		},

		verDetalle(id) {
			this.$router.push(`/carreras/${id}`);
		},
	},
};
</script>
```

---

### Inscribirse a una Carrera

```vue
<template>
	<div class="inscripcion-form">
		<h2>Inscribirse a {{ carrera.nombre_carrera }}</h2>

		<form @submit.prevent="inscribirse">
			<!-- Selección de vehículo -->
			<div class="form-group">
				<label>Vehículo</label>
				<select v-model="formulario.id_vehicle" required>
					<option value="">Seleccione un vehículo</option>
					<option
						v-for="vehiculo in vehiculos"
						:key="vehiculo.id_vehicle"
						:value="vehiculo.id_vehicle"
					>
						{{ vehiculo.marca }} {{ vehiculo.nombre }}
					</option>
				</select>
			</div>

			<!-- Método de pago -->
			<div class="form-group">
				<label>Método de Pago</label>
				<select v-model="formulario.metodo_pago" required>
					<option value="tarjeta_credito">Tarjeta de Crédito</option>
					<option value="tarjeta_debito">Tarjeta de Débito</option>
					<option value="transferencia">Transferencia</option>
					<option value="efectivo">Efectivo</option>
				</select>
			</div>

			<!-- Entrega del vehículo -->
			<div class="form-group">
				<label>
					<input
						type="checkbox"
						v-model="formulario.requiere_entrega"
					/>
					Requiero entrega del vehículo en el circuito
				</label>
			</div>

			<!-- Dirección (si requiere entrega) -->
			<div v-if="formulario.requiere_entrega" class="direccion-fields">
				<div class="form-group">
					<label>Dirección</label>
					<input
						v-model="formulario.direccion_entrega"
						type="text"
						required
					/>
				</div>
				<div class="form-group">
					<label>Ciudad</label>
					<input
						v-model="formulario.ciudad_entrega"
						type="text"
						required
					/>
				</div>
				<div class="form-group">
					<label>Código Postal</label>
					<input
						v-model="formulario.codigo_postal_entrega"
						type="text"
						required
					/>
				</div>
			</div>

			<!-- Resumen -->
			<div class="resumen">
				<h3>Resumen</h3>
				<p><strong>Carrera:</strong> {{ carrera.nombre_carrera }}</p>
				<p>
					<strong>Fecha:</strong>
					{{ formatearFecha(carrera.fecha_inicio) }}
				</p>
				<p><strong>Circuito:</strong> {{ carrera.circuito }}</p>
				<p>
					<strong>Precio:</strong> ${{ carrera.precio_inscripcion }}
				</p>
			</div>

			<!-- Botón -->
			<button type="submit" class="btn-primary" :disabled="procesando">
				{{ procesando ? "Procesando..." : "Confirmar Inscripción" }}
			</button>
		</form>
	</div>
</template>

<script>
import { getAuth } from "firebase/auth";
import inscripcionesService from "@/services/inscripcionesService";
import carrerasService from "@/services/carrerasService";

export default {
	name: "InscripcionForm",

	data() {
		return {
			carrera: {},
			vehiculos: [], // Obtener de tu backend
			formulario: {
				id_piloto: null, // Se obtiene del piloto autenticado
				id_carrera: this.$route.params.id,
				id_vehicle: "",
				metodo_pago: "tarjeta_credito",
				requiere_entrega: false,
				direccion_entrega: "",
				ciudad_entrega: "",
				codigo_postal_entrega: "",
			},
			procesando: false,
		};
	},

	async mounted() {
		await this.cargarCarrera();
		await this.obtenerIdPiloto();
	},

	methods: {
		async cargarCarrera() {
			try {
				const response = await carrerasService.obtenerDetalle(
					this.$route.params.id
				);
				if (response.success) {
					this.carrera = response.data.carrera;
				}
			} catch (error) {
				console.error("Error al cargar carrera:", error);
			}
		},

		async obtenerIdPiloto() {
			// Obtener piloto desde Firebase y tu backend
			const auth = getAuth();
			const user = auth.currentUser;

			if (user) {
				// Aquí deberías obtener el id_piloto de tu tabla pilotos
				// usando el firebase_uid del usuario autenticado
				this.formulario.id_piloto = 1; // Ejemplo
			}
		},

		async inscribirse() {
			this.procesando = true;

			try {
				const response = await inscripcionesService.crear(
					this.formulario
				);

				if (response.success) {
					alert(
						`¡Inscripción exitosa! Tu número de competidor es: ${response.data.numero_competidor}`
					);
					this.$router.push("/mis-inscripciones");
				}
			} catch (error) {
				console.error("Error en inscripción:", error);
				alert(error.message || "Error al procesar inscripción");
			} finally {
				this.procesando = false;
			}
		},

		formatearFecha(fecha) {
			return new Date(fecha).toLocaleDateString("es-AR");
		},
	},
};
</script>
```

---

### Mis Inscripciones

```vue
<template>
	<div class="mis-inscripciones">
		<h2>Mis Inscripciones</h2>

		<!-- Tabs -->
		<div class="tabs">
			<button
				@click="tabActual = 'proximas'"
				:class="{ active: tabActual === 'proximas' }"
			>
				Próximas ({{ agrupadas.proximas?.length || 0 }})
			</button>
			<button
				@click="tabActual = 'en_curso'"
				:class="{ active: tabActual === 'en_curso' }"
			>
				En Curso ({{ agrupadas.en_curso?.length || 0 }})
			</button>
			<button
				@click="tabActual = 'finalizadas'"
				:class="{ active: tabActual === 'finalizadas' }"
			>
				Finalizadas ({{ agrupadas.finalizadas?.length || 0 }})
			</button>
			<button
				@click="tabActual = 'pendientes_pago'"
				:class="{ active: tabActual === 'pendientes_pago' }"
			>
				Pendientes de Pago ({{
					agrupadas.pendientes_pago?.length || 0
				}})
			</button>
		</div>

		<!-- Lista según tab -->
		<div v-if="cargando" class="loading">Cargando...</div>

		<div v-else class="inscripciones-list">
			<div
				v-for="insc in agrupadas[tabActual]"
				:key="insc.id_compra"
				class="inscripcion-card"
			>
				<div class="numero-competidor">
					#{{ insc.numero_competidor }}
				</div>
				<h3>{{ insc.nombre_carrera }}</h3>
				<p>{{ insc.descripcion_carrera }}</p>

				<div class="info">
					<span>📍 {{ insc.circuito }}, {{ insc.ciudad }}</span>
					<span>📅 {{ formatearFecha(insc.fecha_inicio) }}</span>
					<span>🏎️ {{ insc.marca }} {{ insc.vehiculo }}</span>
					<span>💰 ${{ insc.monto_total }}</span>
				</div>

				<!-- Estado de pago -->
				<div class="estado-pago" :class="insc.estado_pago">
					{{ insc.estado_pago }}
				</div>

				<!-- Entrega (si aplica) -->
				<div v-if="insc.id_entrega" class="entrega-info">
					<strong>Entrega:</strong> {{ insc.estado_entrega }}
					<br />
					<small
						>Estimada:
						{{ formatearFecha(insc.fecha_entrega_estimada) }}</small
					>
				</div>

				<!-- Resultados (si finalizó) -->
				<div v-if="insc.id_resultado" class="resultados">
					<strong>Posición Final:</strong> {{ insc.posicion_final }}°
					<br />
					<strong>Puntos:</strong> {{ insc.puntos_obtenidos }}
				</div>
			</div>

			<div v-if="agrupadas[tabActual]?.length === 0" class="no-results">
				No tienes inscripciones en esta categoría
			</div>
		</div>
	</div>
</template>

<script>
import { getAuth } from "firebase/auth";
import inscripcionesService from "@/services/inscripcionesService";

export default {
	name: "MisInscripciones",

	data() {
		return {
			inscripciones: [],
			agrupadas: {},
			tabActual: "proximas",
			cargando: false,
		};
	},

	mounted() {
		this.cargarInscripciones();
	},

	methods: {
		async cargarInscripciones() {
			this.cargando = true;

			try {
				const auth = getAuth();
				const user = auth.currentUser;

				if (!user) {
					this.$router.push("/login");
					return;
				}

				const response = await inscripcionesService.misInscripciones(
					user.uid
				);

				if (response.success) {
					this.inscripciones = response.data.inscripciones;
					this.agrupadas = response.data.agrupadas;
				}
			} catch (error) {
				console.error("Error al cargar inscripciones:", error);
				alert("Error al cargar inscripciones");
			} finally {
				this.cargando = false;
			}
		},

		formatearFecha(fecha) {
			return new Date(fecha).toLocaleDateString("es-AR");
		},
	},
};
</script>
```

---

## 🔥 Integración con Firebase

Crear `src/firebase.js`:

```javascript
import { initializeApp } from "firebase/app";
import { getAuth } from "firebase/auth";

const firebaseConfig = {
	apiKey: "TU_API_KEY",
	authDomain: "TU_AUTH_DOMAIN",
	projectId: "TU_PROJECT_ID",
	storageBucket: "TU_STORAGE_BUCKET",
	messagingSenderId: "TU_SENDER_ID",
	appId: "TU_APP_ID",
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);

export { auth };
```

---

## ⚙️ Variables de Entorno

Crear `.env.local`:

```
VITE_API_BASE_URL=http://localhost/Podium_WEB_Project/backend
VITE_FIREBASE_API_KEY=tu_api_key
VITE_FIREBASE_AUTH_DOMAIN=tu_auth_domain
VITE_FIREBASE_PROJECT_ID=tu_project_id
```

Actualizar `src/services/api.js`:

```javascript
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;
```

---

## 🚀 Ejecución

```bash
# Backend (XAMPP)
# 1. Iniciar Apache y MySQL en XAMPP
# 2. Importar database/podium_db.sql

# Frontend (Vue.js)
npm run dev
# Acceder a http://localhost:5173
```

---

## ✅ Checklist de Integración

-   [ ] Axios instalado
-   [ ] Servicios API creados
-   [ ] Firebase configurado
-   [ ] Variables de entorno configuradas
-   [ ] CORS habilitado en backend
-   [ ] Base de datos importada
-   [ ] Usuarios de prueba creados
-   [ ] Probado flujo de inscripción completo

---

¡Tu frontend Vue.js está listo para comunicarse con el backend PHP! 🎉
