# 📊 Base de Datos PODIUM - Documentación Completa

## Versión 2.0 - Mejorada

---

## 🎯 Descripción General

Base de datos MySQL para el sistema de gestión de carreras de autos **PODIUM**. Diseñada para manejar inscripciones, resultados, entregas, vehículos, circuitos y toda la logística de eventos de automovilismo.

---

## 📋 Estructura de Tablas

### **1. pais**

Países disponibles en el sistema.

| Campo      | Tipo         | Descripción                |
| ---------- | ------------ | -------------------------- |
| id_pais    | INT (PK)     | Identificador único        |
| nombre     | VARCHAR(100) | Nombre del país            |
| codigo_iso | VARCHAR(3)   | Código ISO (ARG, USA, BRA) |
| activo     | BOOLEAN      | Estado del registro        |

---

### **2. usuarios (User)**

Personal de gestión del backend.

| Campo      | Tipo         | Descripción                              |
| ---------- | ------------ | ---------------------------------------- |
| id_usuario | INT (PK)     | Identificador único                      |
| nombre     | VARCHAR(100) | Nombre del usuario                       |
| apellido   | VARCHAR(100) | Apellido                                 |
| email      | VARCHAR(150) | Email (UNIQUE)                           |
| password   | VARCHAR(255) | Contraseña hasheada                      |
| rol        | ENUM         | admin, gestor_entregas, supervisor, juez |
| telefono   | VARCHAR(20)  | Teléfono de contacto                     |
| activo     | BOOLEAN      | Estado activo/inactivo                   |

**Notas de seguridad:**

-   Contraseñas con `password_hash()`
-   Usuario demo: `admin@podium.com` / `admin123`

---

### **3. brand (Marca)**

Marcas de vehículos de competición.

| Campo       | Tipo         | Descripción         |
| ----------- | ------------ | ------------------- |
| id_brand    | INT (PK)     | Identificador único |
| nombre      | VARCHAR(100) | Nombre de la marca  |
| descripcion | TEXT         | Descripción         |
| logo_url    | VARCHAR(255) | URL del logo        |

**Ejemplos:** Ferrari, Mercedes, Red Bull, Ford, Chevrolet, Toyota

---

### **4. type (Tipo de Vehículo)**

Categorías de vehículos.

| Campo       | Tipo         | Descripción         |
| ----------- | ------------ | ------------------- |
| id_type     | INT (PK)     | Identificador único |
| nombre      | VARCHAR(100) | Tipo de vehículo    |
| descripcion | TEXT         | Descripción         |

**Ejemplos:** Fórmula, GT, Rally, Stock Car, Karting, Prototype

---

### **5. vehicle (Vehículo)**

Vehículos registrados en el sistema.

| Campo            | Tipo         | Descripción               |
| ---------------- | ------------ | ------------------------- |
| id_vehicle       | INT (PK)     | Identificador único       |
| id_brand         | INT (FK)     | Marca del vehículo        |
| id_type          | INT (FK)     | Tipo de vehículo          |
| modelo           | VARCHAR(100) | Modelo                    |
| año              | INT          | Año de fabricación        |
| numero_chasis    | VARCHAR(50)  | Número de chasis (UNIQUE) |
| color            | VARCHAR(50)  | Color principal           |
| especificaciones | TEXT         | JSON con specs técnicas   |

**Relaciones:**

-   `brand` (N:1) - Cada vehículo tiene una marca
-   `type` (N:1) - Cada vehículo tiene un tipo

---

### **6. categorias (Category)**

Categorías de carreras.

| Campo        | Tipo         | Descripción            |
| ------------ | ------------ | ---------------------- |
| id_categoria | INT (PK)     | Identificador único    |
| nombre       | VARCHAR(100) | Nombre de la categoría |
| descripcion  | TEXT         | Descripción            |
| imagen_url   | VARCHAR(255) | URL de imagen          |
| reglamento   | TEXT         | Reglamento oficial     |

**Ejemplos:** Fórmula 1, Rally Championship, NASCAR, Karting Pro, GT Championship

---

### **7. city (Ciudad)**

Ciudades donde se realizan eventos.

| Campo         | Tipo          | Descripción           |
| ------------- | ------------- | --------------------- |
| id_city       | INT (PK)      | Identificador único   |
| nombre        | VARCHAR(100)  | Nombre de la ciudad   |
| id_pais       | INT (FK)      | País al que pertenece |
| codigo_postal | VARCHAR(20)   | Código postal         |
| latitud       | DECIMAL(10,7) | Coordenadas GPS       |
| longitud      | DECIMAL(10,7) | Coordenadas GPS       |

---

### **8. storage_id (Almacenamiento)**

Lugares de almacenamiento de vehículos/equipamiento.

| Campo      | Tipo         | Descripción                       |
| ---------- | ------------ | --------------------------------- |
| id_storage | INT (PK)     | Identificador único               |
| nombre     | VARCHAR(100) | Nombre del lugar                  |
| id_city    | INT (FK)     | Ciudad donde se ubica             |
| direccion  | VARCHAR(255) | Dirección completa                |
| capacidad  | INT          | Capacidad en vehículos            |
| tipo       | ENUM         | garage, deposito, taller, paddock |

---

### **9. country (Circuito)**

Autódromos y circuitos de carreras.

| Campo                  | Tipo         | Descripción                  |
| ---------------------- | ------------ | ---------------------------- |
| id_country             | INT (PK)     | Identificador único          |
| nombre                 | VARCHAR(150) | Nombre del circuito          |
| id_city                | INT (FK)     | Ciudad donde se ubica        |
| direccion              | VARCHAR(255) | Dirección                    |
| longitud_pista         | DECIMAL(6,3) | Longitud en km               |
| numero_curvas          | INT          | Cantidad de curvas           |
| tipo_pista             | ENUM         | circuito, calle, oval, mixto |
| capacidad_espectadores | INT          | Aforo del público            |
| imagen_url             | VARCHAR(255) | URL de imagen                |

**Ejemplos:** Autódromo Gálvez, Interlagos, Hermanos Rodríguez

---

### **10. pilotos (Pilots)**

Pilotos participantes (clientes del frontend).

| Campo            | Tipo         | Descripción                         |
| ---------------- | ------------ | ----------------------------------- |
| id_piloto        | INT (PK)     | Identificador único                 |
| firebase_uid     | VARCHAR(128) | UID de Firebase Auth (UNIQUE)       |
| nombre           | VARCHAR(100) | Nombre del piloto                   |
| apellido         | VARCHAR(100) | Apellido                            |
| email            | VARCHAR(150) | Email (UNIQUE)                      |
| telefono         | VARCHAR(20)  | Teléfono                            |
| id_pais          | INT (FK)     | País de origen                      |
| fecha_nacimiento | DATE         | Fecha de nacimiento                 |
| numero_licencia  | VARCHAR(50)  | Número de licencia (UNIQUE)         |
| tipo_licencia    | ENUM         | amateur, profesional, internacional |
| foto_url         | VARCHAR(255) | URL de foto de perfil               |

**Integración Firebase:**

-   Login con Firebase Authentication
-   Sincronización mediante `firebase_uid`

---

### **11. carreras (Races)**

Eventos de carreras.

| Campo              | Tipo          | Descripción                                                         |
| ------------------ | ------------- | ------------------------------------------------------------------- |
| id_carrera         | INT (PK)      | Identificador único                                                 |
| nombre             | VARCHAR(150)  | Nombre de la carrera                                                |
| descripcion        | TEXT          | Descripción del evento                                              |
| id_categoria       | INT (FK)      | Categoría de la carrera                                             |
| id_country         | INT (FK)      | Circuito donde se corre                                             |
| fecha_inicio       | DATETIME      | Inicio del evento                                                   |
| fecha_fin          | DATETIME      | Fin del evento                                                      |
| precio_inscripcion | DECIMAL(10,2) | Costo de inscripción                                                |
| imagen_url         | VARCHAR(255)  | Imagen del evento                                                   |
| estado             | ENUM          | programada, inscripciones_abiertas, en_curso, finalizada, cancelada |
| cupo_maximo        | INT           | Máximo de participantes                                             |
| numero_vueltas     | INT           | Vueltas de la carrera                                               |
| distancia_total    | DECIMAL(8,2)  | Distancia total en km                                               |
| premio_primero     | DECIMAL(10,2) | Premio 1º puesto                                                    |
| premio_segundo     | DECIMAL(10,2) | Premio 2º puesto                                                    |
| premio_tercero     | DECIMAL(10,2) | Premio 3º puesto                                                    |
| clima              | VARCHAR(50)   | Condición climática                                                 |
| transmision_url    | VARCHAR(255)  | URL de streaming                                                    |

---

### **12. compras (Inscripciones)**

Registro de inscripciones a carreras.

| Campo             | Tipo          | Descripción                                   |
| ----------------- | ------------- | --------------------------------------------- |
| id_compra         | INT (PK)      | Identificador único                           |
| id_piloto         | INT (FK)      | Piloto inscripto                              |
| id_carrera        | INT (FK)      | Carrera                                       |
| id_vehicle        | INT (FK)      | Vehículo a usar                               |
| numero_competidor | INT           | Número asignado                               |
| fecha_compra      | TIMESTAMP     | Fecha de inscripción                          |
| monto             | DECIMAL(10,2) | Monto pagado                                  |
| estado_pago       | ENUM          | pendiente, completado, reembolsado, cancelado |
| metodo_pago       | VARCHAR(50)   | Método de pago                                |
| comprobante_url   | VARCHAR(255)  | URL del comprobante                           |
| datos_json        | TEXT          | Datos adicionales JSON                        |

**Constraint:** Un piloto no puede inscribirse dos veces a la misma carrera.

---

### **13. bank (Información Bancaria)**

Datos bancarios de pilotos para pagos/reembolsos.

| Campo         | Tipo         | Descripción                            |
| ------------- | ------------ | -------------------------------------- |
| id_bank       | INT (PK)     | Identificador único                    |
| id_piloto     | INT (FK)     | Piloto propietario                     |
| nombre_banco  | VARCHAR(100) | Nombre del banco                       |
| tipo_cuenta   | ENUM         | ahorro, corriente, paypal, mercadopago |
| numero_cuenta | VARCHAR(100) | Número de cuenta                       |
| cbu_cvu       | VARCHAR(50)  | CBU/CVU                                |
| alias         | VARCHAR(50)  | Alias de la cuenta                     |
| titular       | VARCHAR(150) | Titular de la cuenta                   |
| es_principal  | BOOLEAN      | Cuenta principal                       |

---

### **14. resultados (Race Results)**

Resultados de las carreras finalizadas.

| Campo               | Tipo         | Descripción                                |
| ------------------- | ------------ | ------------------------------------------ |
| id_resultado        | INT (PK)     | Identificador único                        |
| id_carrera          | INT (FK)     | Carrera                                    |
| id_piloto           | INT (FK)     | Piloto                                     |
| id_vehicle          | INT (FK)     | Vehículo usado                             |
| posicion_final      | INT          | Posición final                             |
| posicion_salida     | INT          | Posición en grilla                         |
| tiempo_total        | TIME         | Tiempo total de carrera                    |
| tiempo_mejor_vuelta | TIME         | Mejor tiempo de vuelta                     |
| vuelta_rapida       | INT          | Nº de vuelta más rápida                    |
| puntos              | INT          | Puntos obtenidos                           |
| vueltas_completadas | INT          | Vueltas terminadas                         |
| diferencia_primero  | TIME         | Diferencia con el 1º                       |
| velocidad_promedio  | DECIMAL(6,2) | Velocidad promedio km/h                    |
| velocidad_maxima    | DECIMAL(6,2) | Velocidad máxima km/h                      |
| estado              | ENUM         | finalizado, abandonado, descalificado, dns |
| motivo_abandono     | TEXT         | Razón de abandono                          |
| penalizaciones      | TEXT         | Penalizaciones aplicadas                   |

---

### **15. lap (Vueltas)**

Registro de vueltas individuales por piloto.

| Campo              | Tipo         | Descripción                 |
| ------------------ | ------------ | --------------------------- |
| id_lap             | INT (PK)     | Identificador único         |
| id_resultado       | INT (FK)     | Resultado asociado          |
| numero_vuelta      | INT          | Número de vuelta            |
| tiempo_vuelta      | TIME         | Tiempo de la vuelta         |
| velocidad_promedio | DECIMAL(6,2) | Velocidad promedio          |
| sector_1           | TIME         | Tiempo sector 1             |
| sector_2           | TIME         | Tiempo sector 2             |
| sector_3           | TIME         | Tiempo sector 3             |
| posicion_en_vuelta | INT          | Posición al terminar vuelta |
| incidente          | TEXT         | Descripción de incidentes   |

---

### **16. entregas (Deliveries)**

Gestión de entregas de kits/materiales de carrera.

| Campo              | Tipo         | Descripción                                                      |
| ------------------ | ------------ | ---------------------------------------------------------------- |
| id_entrega         | INT (PK)     | Identificador único                                              |
| id_compra          | INT (FK)     | Compra asociada                                                  |
| id_city            | INT (FK)     | Ciudad de destino                                                |
| direccion_envio    | VARCHAR(255) | Dirección completa                                               |
| codigo_postal      | VARCHAR(20)  | CP                                                               |
| referencias        | TEXT         | Referencias de entrega                                           |
| estado             | ENUM         | pendiente, preparando, en_camino, entregado, devuelto, cancelado |
| fecha_despacho     | DATETIME     | Fecha de despacho                                                |
| fecha_entrega      | DATETIME     | Fecha de entrega                                                 |
| id_usuario_gestor  | INT (FK)     | Gestor asignado                                                  |
| empresa_transporte | VARCHAR(100) | Empresa de transporte                                            |
| tracking_number    | VARCHAR(100) | Número de seguimiento                                            |
| costo_envio        | DECIMAL(8,2) | Costo del envío                                                  |
| firma_receptor     | VARCHAR(255) | URL de firma digital                                             |

---

## 🔗 Relaciones Principales

```
pais (1) ──→ (N) city ──→ (N) country (circuitos)
                 │
                 └──→ (N) storage_id
                 └──→ (N) pilotos
                 └──→ (N) entregas

brand (1) ──→ (N) vehicle (N) ←── type (1)

categorias (1) ──→ (N) carreras

country (1) ──→ (N) carreras

pilotos (N) ←──[compras]──→ (N) carreras
                 │
                 └──→ (1) vehicle

compras (1) ──→ (1) entregas
compras (1) ──→ (1) resultados

resultados (1) ──→ (N) lap

usuarios (1) ──→ (N) entregas (gestor)

pilotos (1) ──→ (N) bank
```

---

## 📊 Vistas SQL Disponibles

### 1. `v_carreras_completas`

Información completa de carreras con categoría, circuito, ciudad, país e inscriptos.

### 2. `v_compras_completas`

Inscripciones con datos del piloto, carrera, vehículo y ubicación.

### 3. `v_entregas_completas`

Entregas con datos completos de piloto, carrera, destino y gestor.

### 4. `v_resultados_completos`

Resultados con información del piloto, carrera, vehículo y ubicación.

### 5. `v_ranking_pilotos`

Ranking general de pilotos con estadísticas (puntos, victorias, podios).

### 6. `v_estadisticas_vueltas`

Estadísticas de vueltas por piloto en cada carrera.

---

## 🚀 Instalación

### Paso 1: Iniciar XAMPP

-   Abrir XAMPP Control Panel
-   Iniciar **Apache** y **MySQL**

### Paso 2: Importar Base de Datos

```bash
# Opción 1: phpMyAdmin
http://localhost/phpmyadmin
# Importar: database/podium_db.sql

# Opción 2: Consola MySQL
mysql -u root < database/podium_db.sql
```

### Paso 3: Verificar

```sql
USE podium_db;
SHOW TABLES;
SELECT * FROM v_carreras_completas;
```

---

## 📦 Datos Incluidos

-   ✅ 8 países
-   ✅ 3 usuarios gestores
-   ✅ 6 marcas de vehículos
-   ✅ 6 tipos de vehículos
-   ✅ 5 vehículos de ejemplo
-   ✅ 5 categorías de carreras
-   ✅ 8 ciudades
-   ✅ 3 almacenamientos
-   ✅ 5 circuitos
-   ✅ 5 pilotos
-   ✅ 4 carreras
-   ✅ 5 inscripciones
-   ✅ 3 datos bancarios
-   ✅ 3 entregas
-   ✅ Resultados y vueltas de ejemplo

---

## 🔐 Credenciales de Prueba

**Administrador:**

-   Email: `admin@podium.com`
-   Password: `admin123`

**Gestor de Entregas:**

-   Email: `gestor@podium.com`
-   Password: `admin123`

**Juez:**

-   Email: `juez@podium.com`
-   Password: `admin123`

⚠️ **Cambiar en producción**

---

## 🎯 Próximos Pasos

1. ✅ Base de datos creada e importada
2. ⏳ Crear backend PHP
3. ⏳ Configurar Firebase Authentication
4. ⏳ Integrar Vue.js con el backend
5. ⏳ Implementar `Terminal_carga_productos.php`

---

**Versión:** 2.0  
**Última actualización:** Noviembre 2025  
**Sistema:** PODIUM Racing Management
