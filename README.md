# 🏁 *Podium*

Sistema web completo de gestión de resultados de carreras desarrollado con **Vue.js 3**, **PHP** y **MySQL**.

## 📋 Descripción

Podium es una plataforma moderna que permite gestionar y visualizar resultados de carreras de automovilismo. Incluye:

- 🔐 **Autenticación** con Firebase
- 👥 **Sistema de roles** (Administrador/Usuario)
- 📊 **Panel de administración** completo con CRUD
- 🎨 **Interfaz moderna** y responsive
- 🔒 **Protección de rutas** basada en roles
- 📱 **Diseño mobile-first**

## 🚀 Características

### Para Usuarios
- Ver categorías de carreras (TC, TCP, TCPK)
- Consultar carreras y resultados en tiempo real
- Interfaz intuitiva y fácil de usar
- Diseño responsive para todos los dispositivos

### Para Administradores
- Panel de administración completo
- Gestión de categorías, vehículos y carreras
- CRUD completo para todas las entidades
- Actualización de posiciones y resultados
- Gestión de usuarios y roles

## 🛠️ Tecnologías

### Frontend
- **Vue.js 3** (Composition API)
- **Vue Router 4** (navegación con protección de rutas)
- **Vite 7** (bundler ultrarrápido)
- **Firebase** (autenticación)

### Backend
- **PHP 7.4+** (API REST)
- **MySQL 5.7+** (base de datos)
- **PDO** (conexión segura a BD)

## 📦 Instalación

### Prerrequisitos

- Node.js 16+
- PHP 7.4+
- MySQL 5.7+
- Apache con mod_rewrite
- Composer (opcional)

### 1. Clonar el repositorio

```bash
git clone <repository-url>
cd Podium_WEB_Project
```

### 2. Configurar Frontend

```bash
# Instalar dependencias
npm install

# Copiar archivo de configuración (si usas variables de entorno)
# y editar con tus credenciales de Firebase
```

### 3. Configurar Backend PHP

1. **Importar base de datos:**
   - Abre phpMyAdmin
   - Crea una base de datos llamada `podium`
   - Importa el archivo `database/podium.sql`

2. **Configurar conexión:**
   - Edita `backend/config/config.php`
   - Actualiza las credenciales de MySQL:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'podium');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

3. **Configurar Apache:**
   - Asegúrate de que el backend esté accesible en `http://localhost/backend`
   - Verifica que `.htaccess` esté habilitado
   - Habilita `mod_rewrite`

### 4. Crear Usuario Administrador

1. Registra un usuario desde el frontend
2. Copia el UID de Firebase del usuario (puedes verlo en la consola al hacer login)
3. Actualiza en MySQL:
```sql
INSERT INTO users (email, firebase_uid, role) VALUES 
('admin@podium.com', 'TU_FIREBASE_UID_AQUI', 'admin');
```

### 5. Iniciar Aplicación

```bash
# Modo desarrollo
npm run dev

# Compilar para producción
npm run build

# Preview de producción
npm run preview
```

La aplicación estará disponible en `http://localhost:5173`

## 📁 Estructura del Proyecto

```
Podium_WEB_Project/
├── backend/                    # Backend PHP
│   ├── config/                # Configuración
│   │   ├── config.php        # Config general y CORS
│   │   └── database.php      # Conexión MySQL
│   ├── controllers/          # Controladores
│   │   ├── UserController.php
│   │   ├── CategoryController.php
│   │   ├── VehicleController.php
│   │   ├── RaceController.php
│   │   └── PositionController.php
│   ├── middleware/           # Middleware
│   │   └── auth.php         # Autenticación
│   ├── index.php            # Router principal
│   ├── .htaccess           # Config Apache
│   └── README.md           # Docs del backend
│
├── database/                 # Scripts SQL
│   └── podium.sql          # Estructura completa
│
├── src/                     # Frontend Vue
│   ├── components/         # Componentes reutilizables
│   │   ├── HeaderComponent.vue
│   │   ├── FooterComponent.vue
│   │   ├── CategorieComponent.vue
│   │   └── RacesComponent.vue
│   ├── views/              # Vistas principales
│   │   ├── Home.vue
│   │   ├── about.vue
│   │   ├── categories.vue
│   │   ├── races.vue
│   │   ├── login.vue
│   │   ├── register.vue
│   │   └── admin/         # Vistas de administración
│   │       ├── AdminLayout.vue
│   │       ├── Dashboard.vue
│   │       ├── CategoriesManagement.vue
│   │       ├── VehiclesManagement.vue
│   │       └── RacesManagement.vue
│   ├── composables/       # Composables (lógica compartida)
│   │   ├── useAuth.js    # Autenticación global
│   │   ├── getCategories.js
│   │   └── useRaceData.js
│   ├── services/         # Servicios API
│   │   ├── userService.js
│   │   ├── categoryService.js
│   │   ├── vehicleService.js
│   │   ├── raceService.js
│   │   └── positionService.js
│   ├── config/          # Configuración
│   │   ├── firebase.js # Config Firebase
│   │   └── api.js      # Config API
│   ├── router/         # Rutas
│   │   └── index.js   # Configuración de rutas
│   ├── data/          # Datos locales (legacy)
│   │   └── database.js
│   ├── App.vue        # Componente principal
│   ├── main.js       # Punto de entrada
│   └── style.css     # Estilos globales
│
├── public/            # Archivos estáticos
│   ├── icon.png
│   ├── tc.jpg
│   ├── tcp.jpg
│   └── tcpk.jpg
│
├── index.html        # HTML principal
├── package.json      # Dependencias npm
├── vite.config.js   # Configuración Vite
└── README.md        # Este archivo
```

## 🔑 API Endpoints

### Públicos (sin autenticación)
- `GET /categories` - Listar categorías
- `GET /categories/{id}` - Obtener categoría
- `GET /vehicles` - Listar vehículos
- `GET /races` - Listar carreras
- `GET /races/{categoryId}/positions` - Carreras con posiciones
- `POST /users` - Crear/actualizar usuario

### Protegidos (requieren autenticación)
- `GET /users/current` - Usuario actual
- Todos los endpoints de creación, edición y eliminación

### Solo Administradores
- CRUD de categorías: `POST`, `PUT`, `DELETE /categories`
- CRUD de vehículos: `POST`, `PUT`, `DELETE /vehicles`
- CRUD de carreras: `POST`, `PUT`, `DELETE /races`
- CRUD de posiciones: `POST`, `PUT`, `DELETE /positions`
- Gestión de usuarios: `GET /users`, `PUT /users/{id}/role`

## 🎨 Paleta de Colores

```css
#000000 — Negro (fondo principal)
#1E1E1E — Negro suave (header/footer)
#323232 — Gris oscuro (fondos secundarios)
#c1b0cc — Lila claro
#503f70 — Violeta oscuro
#548aba — Azul medio
#ff00dd — Fucsia intenso (acentos)
#c32495 — Fucsia oscuro
```

## 🔐 Seguridad

- ✅ Credenciales de Firebase en variables de entorno (recomendado)
- ✅ Autenticación mediante Firebase Auth
- ✅ Validación de roles en backend
- ✅ Protección de rutas en frontend
- ✅ Prepared statements (PDO) para prevenir SQL Injection
- ✅ Headers CORS configurados
- ⚠️ En producción: deshabilitar `display_errors` en PHP
- ⚠️ En producción: usar HTTPS

## 👥 Sistema de Roles

### Usuario Regular
- Ver categorías y carreras
- Consultar posiciones
- Acceso a toda la información pública

### Administrador
- Todas las funciones de usuario regular
- Acceso al panel de administración (`/admin`)
- CRUD completo de categorías, vehículos y carreras
- Gestión de posiciones y resultados
- Gestión de roles de usuarios

## 🐛 Solución de Problemas

### Error de conexión a la base de datos
- Verifica las credenciales en `backend/config/config.php`
- Asegúrate de que MySQL esté ejecutándose
- Verifica que la base de datos `podium` exista

### Error 404 en el backend
- Verifica que `.htaccess` esté habilitado
- Asegúrate de que `mod_rewrite` esté activo en Apache
- Verifica la ruta del backend en `src/config/api.js`

### No puedo acceder al panel de admin
- Verifica que tu usuario tenga rol `admin` en la tabla `users`
- Asegúrate de estar autenticado
- Revisa la consola del navegador para errores

### CORS errors
- Verifica la configuración en `backend/config/config.php`
- Añade el origen de tu frontend a `ALLOWED_ORIGINS`

## 📝 Licencia

© 2025 MD Software Solutions. Todos los derechos reservados.

## 👨‍💻 Autor

Desarrollado por MD Software Solutions

---

**¿Necesitas ayuda?** Abre un issue en el repositorio.
