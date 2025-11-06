# PODIUM - Documentación del Backend

Sistema de gestión de carreras desarrollado con PHP y MySQL.

## 📋 Índice

-   [Configuración](#configuración)
-   [Autenticación](#autenticación)
-   [API Endpoints](#api-endpoints)
-   [Códigos de Estado](#códigos-de-estado)
-   [Seguridad](#seguridad)

---

## ⚙️ Configuración

### Requisitos

-   PHP 7.4 o superior
-   MySQL 5.7 o superior
-   XAMPP (recomendado)
-   Extensiones PHP: mysqli, gd, session

### Instalación

1. **Importar base de datos**

    ```bash
    mysql -u root -p < database/podium_db.sql
    ```

2. **Configurar conexión**
   Editar `backend/config/conexion.php`:

    ```php
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $database = 'podium_db';
    ```

3. **Iniciar servidor**
    - Iniciar XAMPP (Apache + MySQL)
    - Acceder a: `http://localhost/Podium_WEB_Project/backend/`

---

## 🔐 Autenticación

### Backend (Personal de Gestión)

El backend utiliza **sesiones PHP** con CAPTCHA para autenticación de:

-   Administradores
-   Gestores de entregas
-   Supervisores
-   Jueces

#### Login

```http
POST /backend/api/auth/login.php
Content-Type: application/json

{
  "email": "admin@podium.com",
  "password": "admin123",
  "captcha": "ABC123"
}
```

**Respuesta exitosa:**

```json
{
	"success": true,
	"data": {
		"mensaje": "Login exitoso",
		"usuario": {
			"id_usuario": 1,
			"nombre": "Juan",
			"apellido": "Pérez",
			"email": "admin@podium.com",
			"rol": "admin"
		}
	}
}
```

#### Verificar Sesión

```http
GET /backend/api/auth/verificar-sesion.php
```

#### Logout

```http
POST /backend/api/auth/logout.php
```

#### CAPTCHA

```http
GET /backend/api/auth/captcha.php
```

Genera una imagen PNG con código de 6 caracteres almacenado en sesión.

---

### Frontend (Pilotos)

El frontend utiliza **Firebase Authentication** (implementado en Vue.js).

---

## 🚀 API Endpoints

### Carreras

#### Listar Carreras

```http
GET /backend/api/carreras/listar.php
```

**Query Parameters:**

-   `estado` (opcional): `inscripciones_abiertas`, `en_curso`, `finalizada`, etc.
-   `categoria` (opcional): ID de categoría
-   `fecha_desde` (opcional): Fecha inicio filtro (YYYY-MM-DD)
-   `fecha_hasta` (opcional): Fecha fin filtro (YYYY-MM-DD)

**Respuesta:**

```json
{
	"success": true,
	"data": {
		"carreras": [
			{
				"id_carrera": 1,
				"nombre_carrera": "Gran Premio de Monza",
				"descripcion": "...",
				"fecha_inicio": "2024-03-15 14:00:00",
				"precio_inscripcion": 5000.0,
				"categoria": "Fórmula 1",
				"circuito": "Autodromo Nazionale di Monza",
				"ciudad": "Monza",
				"pais": "Italia",
				"cupo_maximo": 20,
				"inscriptos": 12,
				"cupos_disponibles": 8,
				"tiene_cupos": true
			}
		],
		"total": 5
	}
}
```

#### Detalle de Carrera

```http
GET /backend/api/carreras/detalle.php?id=1
```

**Respuesta:**

```json
{
	"success": true,
	"data": {
		"carrera": {
			/* detalles completos */
		},
		"pilotos": [
			/* pilotos inscritos */
		],
		"resultados": [
			/* resultados si finalizó */
		]
	}
}
```

---

### Categorías

#### Listar Categorías

```http
GET /backend/api/categorias/listar.php
```

**Respuesta:**

```json
{
	"success": true,
	"data": {
		"categorias": [
			{
				"id_categoria": 1,
				"nombre": "Fórmula 1",
				"descripcion": "...",
				"imagen_url": "...",
				"total_carreras": 10,
				"carreras_abiertas": 3
			}
		],
		"total": 5
	}
}
```

---

### Inscripciones (Terminal de Carga)

#### Crear Inscripción

```http
POST /backend/Terminal_carga_productos.php
Content-Type: application/json

{
  "id_piloto": 1,
  "id_carrera": 2,
  "id_vehicle": 3,
  "metodo_pago": "tarjeta_credito",
  "requiere_entrega": true,
  "direccion_entrega": "Av. Siempreviva 742",
  "ciudad_entrega": "Springfield",
  "codigo_postal_entrega": "1234"
}
```

**Respuesta exitosa:**

```json
{
	"success": true,
	"data": {
		"mensaje": "Inscripción creada exitosamente",
		"id_compra": 45,
		"numero_competidor": 15,
		"monto_total": 5000.0,
		"id_entrega": 12
	}
}
```

**Validaciones automáticas:**

-   ✅ Piloto existe
-   ✅ Carrera acepta inscripciones
-   ✅ Hay cupos disponibles
-   ✅ Piloto no está ya inscrito
-   ✅ Asignación automática de número de competidor

---

### Mis Inscripciones (Piloto)

```http
GET /backend/api/inscripciones/mis-inscripciones.php?firebase_uid={uid}
```

**Respuesta:**

```json
{
	"success": true,
	"data": {
		"inscripciones": [
			/* todas */
		],
		"agrupadas": {
			"pendientes_pago": [
				/* inscripciones sin pagar */
			],
			"proximas": [
				/* carreras futuras */
			],
			"en_curso": [
				/* carreras actuales */
			],
			"finalizadas": [
				/* carreras pasadas con resultados */
			]
		},
		"total": 8
	}
}
```

---

### Entregas (Backend - Requiere autenticación)

#### Listar Entregas

```http
GET /backend/api/entregas/listar.php
```

**Requiere:** Sesión activa con rol `gestor_entregas`, `supervisor` o `admin`

**Query Parameters:**

-   `estado` (opcional): `pendiente`, `en_proceso`, `lista`, `entregada`
-   `fecha_desde` (opcional)
-   `fecha_hasta` (opcional)
-   `id_carrera` (opcional)

**Respuesta:**

```json
{
	"success": true,
	"data": {
		"entregas": [
			{
				"id_entrega": 1,
				"numero_competidor": 15,
				"piloto_completo": "Juan Pérez",
				"nombre_carrera": "GP Monza",
				"estado": "en_proceso",
				"fecha_entrega_estimada": "2024-03-10",
				"dias_hasta_entrega": 5,
				"esta_atrasada": false,
				"almacen": "Depósito Central"
			}
		],
		"estadisticas": {
			"total": 25,
			"pendientes": 8,
			"en_proceso": 10,
			"listas": 5,
			"entregadas": 2,
			"atrasadas": 3
		}
	}
}
```

#### Detalle de Entrega

```http
GET /backend/api/entregas/detalle.php?id=1
```

**Requiere:** Sesión activa

#### Actualizar Entrega

```http
PUT /backend/api/entregas/actualizar.php
Content-Type: application/json

{
  "id_entrega": 1,
  "estado": "lista",
  "id_storage": 2,
  "observaciones": "Vehículo preparado y listo para retiro"
}
```

**Campos opcionales:**

-   `estado`: `pendiente`, `en_proceso`, `lista`, `entregada`, `devuelta`, `cancelada`
-   `fecha_entrega_estimada`: YYYY-MM-DD
-   `id_storage`: ID del almacén
-   `observaciones`: Texto libre

**Nota:** Al marcar como `entregada`, se registra automáticamente `fecha_entrega_real`.

---

## 📊 Códigos de Estado HTTP

| Código | Significado                             |
| ------ | --------------------------------------- |
| 200    | Operación exitosa                       |
| 201    | Recurso creado                          |
| 400    | Petición inválida (error de validación) |
| 401    | No autenticado                          |
| 403    | Acceso denegado (sin permisos)          |
| 404    | Recurso no encontrado                   |
| 405    | Método HTTP no permitido                |
| 500    | Error interno del servidor              |

---

## 🔒 Seguridad

### Medidas implementadas

1. **Prepared Statements**

    - Todas las consultas SQL usan prepared statements
    - Protección contra inyección SQL

2. **Sanitización**

    - Clase `Database` incluye método `sanitize()`
    - Validación de tipos de datos

3. **Sesiones seguras**

    - `httponly` cookies
    - Timeout de 1 hora
    - Regeneración de ID tras login

4. **CAPTCHA**

    - Obligatorio en login backend
    - Código de 6 caracteres con ruido

5. **Passwords**

    - Hash con `password_hash()` (bcrypt, cost 12)
    - Verificación con `password_verify()`

6. **CORS**

    - Configurado para Vue.js dev server
    - Credenciales permitidas

7. **Transacciones**
    - Operaciones críticas (inscripciones) usan transacciones
    - Rollback automático en caso de error

---

## 🗂️ Estructura de Archivos

```
backend/
├── config/
│   ├── conexion.php         # Clase Database
│   └── config.php            # Constantes y helpers
├── api/
│   ├── auth/
│   │   ├── login.php
│   │   ├── logout.php
│   │   ├── captcha.php
│   │   └── verificar-sesion.php
│   ├── carreras/
│   │   ├── listar.php
│   │   └── detalle.php
│   ├── categorias/
│   │   └── listar.php
│   ├── inscripciones/
│   │   └── mis-inscripciones.php
│   └── entregas/
│       ├── listar.php
│       ├── detalle.php
│       └── actualizar.php
└── Terminal_carga_productos.php   # Endpoint principal inscripciones
```

---

## 📝 Constantes Disponibles

### Estados de Carrera

-   `CARRERA_PROGRAMADA`
-   `CARRERA_INSCRIPCIONES_ABIERTAS`
-   `CARRERA_EN_CURSO`
-   `CARRERA_FINALIZADA`
-   `CARRERA_CANCELADA`

### Estados de Pago

-   `PAGO_PENDIENTE`
-   `PAGO_COMPLETADO`
-   `PAGO_REEMBOLSADO`
-   `PAGO_CANCELADO`

### Estados de Entrega

-   `ENTREGA_PENDIENTE`
-   `ENTREGA_EN_PROCESO`
-   `ENTREGA_LISTA`
-   `ENTREGA_ENTREGADA`
-   `ENTREGA_DEVUELTA`
-   `ENTREGA_CANCELADA`

### Roles de Usuario

-   `ROL_ADMIN`
-   `ROL_GESTOR_ENTREGAS`
-   `ROL_SUPERVISOR`
-   `ROL_JUEZ`

---

## 🛠️ Funciones Helper Disponibles

```php
// Enviar respuesta JSON
sendJSON($data, $status_code);
sendSuccess($data, $message);
sendError($message, $status_code);

// Autenticación
requireLogin();  // Lanza error 401 si no hay sesión

// Seguridad
hashPassword($password);
verifyPassword($password, $hash);

// Utilidades
getJSONInput();  // Obtiene datos JSON del request
logMessage($message, $level);  // Registra en logs
```

---

## 🧪 Testing

### Usuarios de prueba (Backend)

| Email             | Password  | Rol             |
| ----------------- | --------- | --------------- |
| admin@podium.com  | admin123  | admin           |
| gestor@podium.com | gestor123 | gestor_entregas |
| juez@podium.com   | juez123   | juez            |

### Ejemplos con cURL

**Login:**

```bash
curl -X POST http://localhost/Podium_WEB_Project/backend/api/auth/login.php \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@podium.com","password":"admin123","captcha":"ABC123"}' \
  -c cookies.txt
```

**Listar carreras:**

```bash
curl http://localhost/Podium_WEB_Project/backend/api/carreras/listar.php?estado=inscripciones_abiertas
```

**Listar entregas (con sesión):**

```bash
curl http://localhost/Podium_WEB_Project/backend/api/entregas/listar.php \
  -b cookies.txt
```

---

## 📞 Soporte

Para dudas o problemas, revisar:

-   Logs de Apache: `xampp/apache/logs/error.log`
-   Logs de PHP: Ver salida de `logMessage()` en consola
-   Base de datos: Verificar estructura en `database/README_DB.md`

---

**Versión:** 2.0  
**Última actualización:** 2024
