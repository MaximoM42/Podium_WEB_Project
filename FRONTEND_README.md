# PODIUM - Frontend Vue.js

Sistema de gestión de carreras - Frontend desarrollado con Vue.js 3 y Bootstrap 4.

## 🚀 Instalación

### Requisitos Previos

-   Node.js 16+ y npm
-   Backend PHP configurado y corriendo (ver `/backend/README.md`)

### Pasos de Instalación

1. **Instalar dependencias**

    ```bash
    npm install
    ```

2. **Configurar variables de entorno**

    Editar el archivo `.env.local`:

    ```env
    VITE_API_BASE_URL=http://localhost/Podium_WEB_Project/backend
    ```

3. **Iniciar servidor de desarrollo**

    ```bash
    npm run dev
    ```

4. **Acceder a la aplicación**

    Abrir navegador en: `http://localhost:5173`

---

## 📦 Tecnologías Utilizadas

-   **Vue.js 3** - Framework JavaScript progresivo
-   **Vue Router 4** - Enrutamiento SPA
-   **Axios** - Cliente HTTP para API REST
-   **Bootstrap 4.6** - Framework CSS para diseño responsive
-   **Font Awesome 6** - Iconos
-   **Vite** - Build tool y dev server

---

## 📁 Estructura del Proyecto

```
src/
├── assets/                 # Recursos estáticos (imágenes, etc.)
├── components/            # Componentes Vue reutilizables
│   ├── HeaderComponent.vue      # Navegación principal
│   ├── FooterComponent.vue      # Pie de página
│   ├── CarrerasList.vue         # Lista de carreras con filtros
│   ├── CarreraDetalle.vue       # Detalle completo de una carrera
│   └── InscripcionForm.vue      # Formulario de inscripción
├── views/                 # Vistas principales (páginas)
│   ├── Home.vue
│   ├── categories/
│   ├── forum/
│   └── login/
├── services/              # Servicios para consumir la API
│   ├── api.js                   # Configuración de Axios
│   ├── carrerasService.js       # Servicio de carreras
│   ├── categoriasService.js     # Servicio de categorías
│   └── inscripcionesService.js  # Servicio de inscripciones
├── router/                # Configuración de rutas
│   └── index.js
├── styles/                # Estilos CSS globales
├── App.vue               # Componente raíz
└── main.js               # Punto de entrada
```

---

## 🔗 Rutas Disponibles

| Ruta               | Componente      | Descripción                       |
| ------------------ | --------------- | --------------------------------- |
| `/`                | Home            | Página principal                  |
| `/carreras`        | CarrerasList    | Lista de carreras con filtros     |
| `/carreras/:id`    | CarreraDetalle  | Detalle de una carrera específica |
| `/inscripcion/:id` | InscripcionForm | Formulario de inscripción         |
| `/categories`      | Categories      | Categorías                        |
| `/forum`           | Forum           | Foro                              |
| `/login`           | Login           | Inicio de sesión                  |

---

## 🛠️ Componentes Principales

### 1. CarrerasList

**Ubicación:** `src/components/CarrerasList.vue`

**Características:**

-   ✅ Filtros por estado, categoría y fechas
-   ✅ Grid responsive con Bootstrap
-   ✅ Badges de estado con colores
-   ✅ Información de cupos disponibles
-   ✅ Paginación automática
-   ✅ Loading states

**Uso:**

```vue
<CarrerasList />
```

**API Consumida:**

-   `GET /api/carreras/listar.php`
-   `GET /api/categorias/listar.php`

---

### 2. CarreraDetalle

**Ubicación:** `src/components/CarreraDetalle.vue`

**Características:**

-   ✅ Información completa de la carrera
-   ✅ Datos del circuito
-   ✅ Premios por posición
-   ✅ Lista de pilotos inscritos
-   ✅ Resultados finales (si finalizó)
-   ✅ Barra de progreso de cupos
-   ✅ Botón de inscripción condicional

**Uso:**

```vue
<CarreraDetalle />
```

**API Consumida:**

-   `GET /api/carreras/detalle.php?id={id}`

---

### 3. InscripcionForm

**Ubicación:** `src/components/InscripcionForm.vue`

**Características:**

-   ✅ Formulario completo de inscripción
-   ✅ Selección de vehículo
-   ✅ Método de pago
-   ✅ Opción de entrega del vehículo
-   ✅ Validaciones en tiempo real
-   ✅ Modal de confirmación
-   ✅ Asignación automática de número de competidor
-   ✅ Resumen lateral con precio

**Uso:**

```vue
<InscripcionForm />
```

**API Consumida:**

-   `POST /Terminal_carga_productos.php`
-   `GET /api/carreras/detalle.php?id={id}`

---

## 🔌 Servicios API

### api.js

Cliente Axios configurado globalmente:

```javascript
import apiClient from "@/services/api";

// Ejemplo de uso
const response = await apiClient.get("/api/carreras/listar.php");
```

**Características:**

-   ✅ Base URL configurable por entorno
-   ✅ Credenciales incluidas (cookies de sesión)
-   ✅ Interceptores de error globales
-   ✅ Timeout de 15 segundos
-   ✅ Manejo automático de errores 401, 403, 404, 500

---

### carrerasService.js

```javascript
import carrerasService from "@/services/carrerasService";

// Listar todas las carreras
const carreras = await carrerasService.listar();

// Filtrar carreras
const abiertas = await carrerasService.listar({
	estado: "inscripciones_abiertas",
});

// Obtener detalle
const detalle = await carrerasService.obtenerDetalle(1);
```

**Métodos:**

-   `listar(filtros)` - Lista con filtros opcionales
-   `obtenerDetalle(id)` - Detalle completo
-   `obtenerAbiertas()` - Solo con inscripciones abiertas
-   `obtenerPorCategoria(id)` - Filtrar por categoría

---

### categoriasService.js

```javascript
import categoriasService from "@/services/categoriasService";

// Listar categorías
const categorias = await categoriasService.listar();
```

---

### inscripcionesService.js

```javascript
import inscripcionesService from "@/services/inscripcionesService";

// Crear inscripción
const datos = {
	id_piloto: 1,
	id_carrera: 2,
	id_vehicle: 3,
	metodo_pago: "tarjeta_credito",
	requiere_entrega: true,
	direccion_entrega: "Av. Siempreviva 742",
	ciudad_entrega: "Springfield",
	codigo_postal_entrega: "1234",
};

const resultado = await inscripcionesService.crear(datos);
// resultado.data.numero_competidor

// Obtener mis inscripciones (requiere Firebase UID)
const misCarreras = await inscripcionesService.misInscripciones(firebaseUid);
```

---

## 🎨 Diseño y Estilos

### Bootstrap 4

Todos los componentes usan clases de Bootstrap 4:

-   **Grid System:** `container`, `row`, `col-md-*`
-   **Cards:** `card`, `card-body`, `card-header`
-   **Buttons:** `btn`, `btn-primary`, `btn-success`
-   **Forms:** `form-control`, `form-group`
-   **Badges:** `badge`, `badge-success`, `badge-warning`
-   **Modals:** `modal`, `modal-dialog`
-   **Alerts:** `alert`, `alert-success`

### Font Awesome

Iconos disponibles:

```html
<i class="fas fa-flag-checkered"></i>
<!-- Carreras -->
<i class="fas fa-trophy"></i>
<!-- Premios -->
<i class="fas fa-calendar-alt"></i>
<!-- Fechas -->
<i class="fas fa-map-marker-alt"></i>
<!-- Ubicación -->
<i class="fas fa-dollar-sign"></i>
<!-- Precio -->
<i class="fas fa-users"></i>
<!-- Cupos -->
```

### Estilos Personalizados

Los componentes incluyen estilos scoped para:

-   Hover effects en cards
-   Animaciones de transición
-   Truncado de texto
-   Sticky positioning

---

## 🔒 Autenticación (Pendiente)

### Firebase (Frontend)

Para implementar autenticación de pilotos:

1. **Configurar Firebase**

    Crear `src/firebase.js`:

    ```javascript
    import { initializeApp } from "firebase/app";
    import { getAuth } from "firebase/auth";

    const firebaseConfig = {
    	apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
    	// ... otros valores
    };

    const app = initializeApp(firebaseConfig);
    export const auth = getAuth(app);
    ```

2. **Obtener usuario actual**

    ```javascript
    import { getAuth } from "firebase/auth";

    const auth = getAuth();
    const user = auth.currentUser;

    if (user) {
    	console.log(user.uid); // Usar para API calls
    }
    ```

---

## 📊 Flujo de Inscripción

```
1. Usuario visita /carreras
   ↓
2. Ve lista de carreras con filtros
   ↓
3. Click en "Ver Detalle"
   ↓
4. Ve información completa en /carreras/:id
   ↓
5. Click en "Inscribirme"
   ↓
6. Formulario en /inscripcion/:id
   ↓
7. Completa datos (vehículo, pago, entrega)
   ↓
8. Submit → POST a /Terminal_carga_productos.php
   ↓
9. Backend valida y crea inscripción
   ↓
10. Modal muestra número de competidor
    ↓
11. Redirección a /mis-inscripciones
```

---

## 🧪 Testing

### Datos de Prueba

El backend incluye datos de ejemplo:

-   5 carreras
-   3 categorías (Fórmula 1, Rally, GT)
-   10 pilotos
-   15 vehículos

### Probar Inscripción

1. Ir a `/carreras`
2. Seleccionar carrera con estado "inscripciones_abiertas"
3. Click en "Ver Detalle"
4. Click en "Inscribirme"
5. Completar formulario
6. Verificar modal de éxito con número de competidor

---

## 🛠️ Scripts Disponibles

```bash
# Desarrollo
npm run dev          # Inicia servidor dev en http://localhost:5173

# Producción
npm run build        # Genera build optimizado en /dist
npm run preview      # Preview del build de producción
```

---

## 🐛 Solución de Problemas

### Error: "Cannot read properties of undefined"

**Causa:** Backend no está corriendo o URL incorrecta

**Solución:**

1. Verificar que XAMPP esté iniciado (Apache + MySQL)
2. Revisar `.env.local` → `VITE_API_BASE_URL`
3. Abrir consola del navegador para ver errores de red

---

### Error de CORS

**Causa:** Backend no permite peticiones desde el frontend

**Solución:**
Verificar en `backend/config/config.php`:

```php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
```

---

### Bootstrap no se aplica

**Causa:** CDN no cargó o conflictos de CSS

**Solución:**

1. Verificar conexión a internet
2. Revisar `index.html` → links de Bootstrap
3. Limpiar cache del navegador

---

## 📝 TODO

-   [ ] Implementar autenticación con Firebase
-   [ ] Crear componente "Mis Inscripciones"
-   [ ] Agregar sistema de notificaciones/toasts
-   [ ] Implementar lazy loading de imágenes
-   [ ] Añadir skeleton loaders
-   [ ] Crear página de perfil de piloto
-   [ ] Implementar búsqueda en tiempo real
-   [ ] Agregar modo oscuro
-   [ ] Optimizar bundle size
-   [ ] Añadir tests unitarios

---

## 📞 Soporte

Para problemas o dudas:

-   Revisar console del navegador (F12)
-   Verificar Network tab para ver errores de API
-   Consultar documentación del backend en `/backend/README.md`

---

**Versión:** 1.0  
**Última actualización:** Noviembre 2025
