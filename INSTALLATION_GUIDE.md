# 📖 Guía de Instalación Detallada - Podium

Esta guía te llevará paso a paso por el proceso completo de instalación de Podium.

## 📋 Tabla de Contenidos

1. [Requisitos Previos](#requisitos-previos)
2. [Instalación del Frontend](#instalación-del-frontend)
3. [Instalación del Backend](#instalación-del-backend)
4. [Configuración de la Base de Datos](#configuración-de-la-base-de-datos)
5. [Configuración de Firebase](#configuración-de-firebase)
6. [Crear Usuario Administrador](#crear-usuario-administrador)
7. [Verificación](#verificación)
8. [Problemas Comunes](#problemas-comunes)

---

## 1. Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

### Software Necesario

- **Node.js** (versión 16 o superior)
  - Descargar desde: https://nodejs.org/
  - Verificar instalación: `node --version`

- **PHP** (versión 7.4 o superior)
  - En Windows: XAMPP, WAMP o Laragon
  - Verificar instalación: `php --version`

- **MySQL** (versión 5.7 o superior)
  - Incluido en XAMPP/WAMP/Laragon
  - Verificar instalación: abre phpMyAdmin

- **Apache** con mod_rewrite habilitado
  - Incluido en XAMPP/WAMP/Laragon

### Recomendado

- **Git** para clonar el repositorio
- **Visual Studio Code** como editor de código
- **Postman** para probar el API (opcional)

---

## 2. Instalación del Frontend

### Paso 2.1: Clonar el Repositorio

```bash
# Si tienes Git
git clone <repository-url>
cd Podium_WEB_Project

# O descarga el ZIP y extráelo
```

### Paso 2.2: Instalar Dependencias de Node

```bash
# Abrir terminal en la carpeta del proyecto
npm install
```

Esto instalará:
- Vue.js 3
- Vue Router 4
- Firebase 12
- Vite 7
- Y otras dependencias

### Paso 2.3: Configurar Variables de Entorno (Opcional pero Recomendado)

Crea un archivo `.env` en la raíz del proyecto:

```env
# Firebase Configuration
VITE_FIREBASE_API_KEY=tu_api_key_aqui
VITE_FIREBASE_AUTH_DOMAIN=tu_auth_domain_aqui
VITE_FIREBASE_PROJECT_ID=tu_project_id_aqui
VITE_FIREBASE_STORAGE_BUCKET=tu_storage_bucket_aqui
VITE_FIREBASE_MESSAGING_SENDER_ID=tu_messaging_sender_id_aqui
VITE_FIREBASE_APP_ID=tu_app_id_aqui
VITE_FIREBASE_MEASUREMENT_ID=tu_measurement_id_aqui

# Backend API
VITE_API_URL=http://localhost/backend
```

> **Nota:** Si no creas el archivo `.env`, el sistema usará las credenciales de Firebase incluidas en `src/config/firebase.js`

---

## 3. Instalación del Backend

### Paso 3.1: Configurar Servidor Local

**Opción A: Usando XAMPP (Recomendado para Windows)**

1. Instala XAMPP desde https://www.apachefriends.org/
2. Inicia Apache y MySQL desde el panel de control de XAMPP
3. Copia la carpeta `backend` a `C:\xampp\htdocs\`
4. El backend estará disponible en: `http://localhost/backend`

**Opción B: Usando WAMP o Laragon**

Similar a XAMPP, coloca la carpeta `backend` en el directorio `www` correspondiente.

**Opción C: PHP Built-in Server (Solo para desarrollo)**

```bash
cd backend
php -S localhost:8000
```

### Paso 3.2: Verificar que Apache esté Funcionando

Abre en tu navegador:
```
http://localhost/backend
```

Deberías ver una respuesta JSON como:
```json
{
  "success": true,
  "message": "API Podium v1.0",
  "endpoints": {
    "users": "/users",
    "categories": "/categories",
    "vehicles": "/vehicles",
    "races": "/races",
    "positions": "/positions"
  }
}
```

### Paso 3.3: Configurar Credenciales de MySQL

Edita `backend/config/config.php`:

```php
// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'podium');
define('DB_USER', 'root');        // Cambiar si usas otro usuario
define('DB_PASS', '');            // Cambiar si tienes contraseña
define('DB_CHARSET', 'utf8mb4');
```

---

## 4. Configuración de la Base de Datos

### Paso 4.1: Abrir phpMyAdmin

```
http://localhost/phpmyadmin
```

### Paso 4.2: Crear la Base de Datos

1. Click en "Nueva" en el panel izquierdo
2. Nombre: `podium`
3. Cotejamiento: `utf8mb4_unicode_ci`
4. Click en "Crear"

### Paso 4.3: Importar el Script SQL

1. Selecciona la base de datos `podium`
2. Click en la pestaña "Importar"
3. Click en "Seleccionar archivo"
4. Navega y selecciona `database/podium.sql`
5. Click en "Continuar" al final de la página

### Paso 4.4: Verificar Importación

Deberías ver estas tablas creadas:
- `categories` (3 registros)
- `vehicles` (12 registros)
- `races` (10 registros)
- `positions` (19 registros)
- `users` (1 registro)

---

## 5. Configuración de Firebase

### Paso 5.1: Crear Proyecto en Firebase (Si no lo tienes)

1. Ve a https://console.firebase.google.com/
2. Click en "Agregar proyecto"
3. Nombre: "Podium" (o el que prefieras)
4. Sigue los pasos del asistente

### Paso 5.2: Habilitar Authentication

1. En el menú lateral, click en "Authentication"
2. Click en "Comenzar"
3. Habilita "Correo electrónico/contraseña"
4. Guarda

### Paso 5.3: Obtener Credenciales

1. Click en el ícono de configuración (⚙️) > "Configuración del proyecto"
2. En la sección "Tus apps", selecciona la app web (</> icono)
3. Si no tienes una app, click en "Agregar app" > Web
4. Copia las credenciales de `firebaseConfig`

### Paso 5.4: Actualizar Credenciales en el Proyecto

**Opción A: Usando variables de entorno**

Edita el archivo `.env` con tus credenciales

**Opción B: Directamente en el código**

Edita `src/config/firebase.js`:

```javascript
export const firebaseConfig = {
  apiKey: "TU_API_KEY",
  authDomain: "TU_AUTH_DOMAIN",
  projectId: "TU_PROJECT_ID",
  // ... resto de credenciales
};
```

---

## 6. Crear Usuario Administrador

### Paso 6.1: Registrar Usuario desde el Frontend

1. Inicia el frontend: `npm run dev`
2. Abre `http://localhost:5173`
3. Ve a "Register"
4. Registra un usuario (ej: admin@podium.com)

### Paso 6.2: Obtener UID de Firebase

**Método 1: Desde la consola del navegador**

Después de hacer login, abre la consola del navegador (F12) y verás el UID impreso.

**Método 2: Desde Firebase Console**

1. Ve a Firebase Console > Authentication > Users
2. Busca tu usuario
3. Copia el "User UID"

### Paso 6.3: Actualizar Rol en MySQL

En phpMyAdmin, ejecuta:

```sql
UPDATE users 
SET role = 'admin' 
WHERE firebase_uid = 'TU_FIREBASE_UID_AQUI';
```

O inserta directamente:

```sql
INSERT INTO users (email, firebase_uid, role) VALUES 
('admin@podium.com', 'TU_FIREBASE_UID_AQUI', 'admin');
```

### Paso 6.4: Verificar

1. Cierra sesión y vuelve a iniciar sesión
2. Deberías ver un enlace "Admin" en el menú
3. Click en "Admin" para acceder al panel

---

## 7. Verificación

### Checklist de Verificación

- [ ] Frontend corriendo en `http://localhost:5173`
- [ ] Backend respondiendo en `http://localhost/backend`
- [ ] MySQL funcionando con base de datos `podium`
- [ ] Firebase configurado y funcionando
- [ ] Puedes registrarte e iniciar sesión
- [ ] Puedes ver categorías y carreras
- [ ] Usuario admin puede acceder al panel de administración
- [ ] CRUD de categorías funciona
- [ ] CRUD de vehículos funciona
- [ ] CRUD de carreras funciona

### Pruebas Rápidas

1. **Prueba de Categorías:**
   - Ve a http://localhost:5173/categories
   - Deberías ver 3 categorías: TC, TCP, TCPK

2. **Prueba de Carreras:**
   - Click en cualquier categoría
   - Deberías ver las carreras de esa categoría con sus posiciones

3. **Prueba de Admin:**
   - Inicia sesión como admin
   - Ve a /admin
   - Intenta crear una nueva categoría de prueba

---

## 8. Problemas Comunes

### Problema: "Error de conexión a la base de datos"

**Solución:**
1. Verifica que MySQL esté corriendo
2. Verifica credenciales en `backend/config/config.php`
3. Verifica que la base de datos `podium` exista

### Problema: "404 Not Found" en el backend

**Solución:**
1. Verifica que Apache esté corriendo
2. Verifica que `.htaccess` esté en la carpeta `backend`
3. Verifica que `mod_rewrite` esté habilitado en Apache:
   ```apache
   # En httpd.conf, busca y descomenta:
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
4. Reinicia Apache

### Problema: "CORS policy" error

**Solución:**
1. Edita `backend/config/config.php`
2. Añade tu origen a `ALLOWED_ORIGINS`:
   ```php
   define('ALLOWED_ORIGINS', [
       'http://localhost:5173',
       'http://localhost:3000',
   ]);
   ```

### Problema: No puedo acceder al panel de admin

**Solución:**
1. Verifica que tu usuario tenga rol `admin` en MySQL:
   ```sql
   SELECT * FROM users WHERE email = 'tu_email@ejemplo.com';
   ```
2. El campo `role` debe ser `'admin'`
3. Cierra sesión e inicia de nuevo

### Problema: "Firebase: Error (auth/...)"

**Solución:**
1. Verifica que las credenciales de Firebase sean correctas
2. Verifica que "Email/Password" esté habilitado en Firebase Console
3. Verifica que no haya restricciones de API Key en Firebase Console

### Problema: Las imágenes no se cargan

**Solución:**
1. Verifica que las imágenes estén en la carpeta `public/`
2. Las rutas en la base de datos deben ser: `/tc.jpg`, `/tcp.jpg`, etc.

---

## 🎉 ¡Listo!

Si completaste todos los pasos, tu instalación de Podium debería estar funcionando correctamente.

### Próximos Pasos

1. **Añadir más datos:** Usa el panel de admin para añadir más categorías, vehículos y carreras
2. **Personalizar diseño:** Modifica los colores y estilos en `src/style.css`
3. **Configurar para producción:** Lee la guía de despliegue para poner tu app en un servidor real

---

## 📞 Soporte

¿Problemas con la instalación? 

1. Revisa la sección de "Problemas Comunes" arriba
2. Consulta la documentación completa en `README.md`
3. Abre un issue en el repositorio

**¡Disfruta usando Podium!** 🏁

