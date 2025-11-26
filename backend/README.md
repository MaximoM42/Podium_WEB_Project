# Backend PHP - Podium API

API REST para el sistema de gestión de resultados de carreras Podium.

## Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache con mod_rewrite habilitado
- Extensiones PHP: PDO, pdo_mysql

## Instalación

1. **Importar la base de datos:**
   - Abre phpMyAdmin
   - Crea una nueva base de datos llamada `podium`
   - Importa el archivo `database/podium.sql`

2. **Configurar la conexión:**
   - Edita `backend/config/config.php`
   - Actualiza las credenciales de MySQL si es necesario:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'podium');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     ```

3. **Configurar Apache:**
   - Asegúrate de que el archivo `.htaccess` esté habilitado
   - El backend debe estar accesible en `http://localhost/backend` o similar

4. **Crear usuario administrador:**
   - Primero registra un usuario en Firebase desde el frontend
   - Copia el UID de Firebase del usuario
   - Actualiza la tabla `users` en MySQL:
     ```sql
     UPDATE users SET role = 'admin' WHERE firebase_uid = 'TU_FIREBASE_UID';
     ```

## Endpoints del API

### Autenticación
Todas las rutas protegidas requieren el header:
```
Authorization: Bearer {FIREBASE_UID}
```

### Usuarios

- `POST /users` - Crear/actualizar usuario (público)
- `GET /users/current` - Obtener usuario actual (autenticado)
- `GET /users` - Listar usuarios (solo admin)
- `PUT /users/{id}/role` - Cambiar rol (solo admin)

### Categorías

- `GET /categories` - Listar categorías (público)
- `GET /categories/{id}` - Obtener categoría (público)
- `POST /categories` - Crear categoría (solo admin)
- `PUT /categories/{id}` - Actualizar categoría (solo admin)
- `DELETE /categories/{id}` - Eliminar categoría (solo admin)

### Vehículos

- `GET /vehicles` - Listar vehículos (público, ?category_id opcional)
- `GET /vehicles/{id}` - Obtener vehículo (público)
- `POST /vehicles` - Crear vehículo (solo admin)
- `PUT /vehicles/{id}` - Actualizar vehículo (solo admin)
- `DELETE /vehicles/{id}` - Eliminar vehículo (solo admin)

### Carreras

- `GET /races` - Listar carreras (público, ?category_id opcional)
- `GET /races/{id}` - Obtener carrera con posiciones (público)
- `GET /races/{categoryId}/positions` - Carreras de categoría con posiciones (público)
- `POST /races` - Crear carrera (solo admin)
- `PUT /races/{id}` - Actualizar carrera (solo admin)
- `DELETE /races/{id}` - Eliminar carrera (solo admin)

### Posiciones

- `GET /positions/{raceId}` - Obtener posiciones de una carrera (público)
- `POST /positions` - Crear posición (solo admin)
- `PUT /positions/{id}` - Actualizar posición (solo admin)
- `DELETE /positions/{id}` - Eliminar posición (solo admin)
- `POST /positions/bulk` - Actualizar múltiples posiciones (solo admin)

## Estructura de Archivos

```
backend/
├── config/
│   ├── config.php       # Configuración general
│   └── database.php     # Conexión a MySQL
├── controllers/
│   ├── UserController.php
│   ├── CategoryController.php
│   ├── VehicleController.php
│   ├── RaceController.php
│   └── PositionController.php
├── middleware/
│   └── auth.php         # Middleware de autenticación
├── index.php            # Router principal
├── .htaccess           # Configuración Apache
└── README.md
```

## Seguridad

- Las contraseñas se manejan completamente en Firebase
- El backend solo verifica el UID de Firebase
- Los roles (admin/user) se manejan en MySQL
- Las operaciones de escritura requieren rol de admin
- Las lecturas son públicas para permitir que los visitantes vean las carreras

## Notas

- En producción, cambia `display_errors` a 0 en `config.php`
- Configura credenciales de base de datos seguras
- Considera usar HTTPS en producción
- El sistema de autenticación actual es simple; en producción se recomienda verificar tokens JWT de Firebase

