# 🏁 PODIUM Database - Resumen Ejecutivo v2.0

## ✅ Base de Datos Mejorada - Basada en DER Proporcionado

---

## 📦 Lo que se ha creado

### Archivos del Proyecto

```
database/
├── podium_db.sql          (Script SQL principal - 16 tablas)
├── README_DB.md           (Documentación completa)
└── ER_DIAGRAM.md          (Diagrama y relaciones)
```

---

## 🗄️ Estructura de la Base de Datos

### Total: **16 Tablas + 6 Vistas SQL**

### Tablas Principales

#### 📍 **Geografía y Ubicación**

1. **pais** - Países (8 registros de ejemplo)
2. **city** - Ciudades (8 registros)
3. **country** - Circuitos/Autódromos (5 circuitos)
4. **storage_id** - Almacenamientos (3 lugares)

#### 👥 **Usuarios y Pilotos**

5. **usuarios** - Personal de gestión (3 usuarios)
6. **pilotos** - Participantes/Clientes (5 pilotos)
7. **bank** - Información bancaria (3 registros)

#### 🚗 **Vehículos**

8. **brand** - Marcas (6 marcas: Ferrari, Mercedes, etc.)
9. **type** - Tipos de vehículos (6 tipos)
10. **vehicle** - Vehículos registrados (5 vehículos)

#### 🏆 **Carreras y Competencias**

11. **categorias** - Categorías de carreras (5 categorías)
12. **carreras** - Eventos (4 carreras de ejemplo)
13. **compras** - Inscripciones (5 inscripciones)
14. **resultados** - Resultados de carreras (3 resultados)
15. **lap** - Vueltas individuales (5 vueltas de ejemplo)

#### 📦 **Logística**

16. **entregas** - Gestión de envíos (3 entregas)

---

## 🎯 Mejoras Principales vs Versión 1.0

### ✅ Nuevas Tablas Agregadas

-   ✅ **pais** - Gestión de países normalizada
-   ✅ **city** - Ciudades con coordenadas GPS
-   ✅ **country** - Circuitos detallados con características técnicas
-   ✅ **storage_id** - Almacenamientos y paddocks
-   ✅ **brand** - Marcas de vehículos
-   ✅ **type** - Tipos de vehículos
-   ✅ **vehicle** - Vehículos de competición
-   ✅ **bank** - Información bancaria de pilotos
-   ✅ **lap** - Registro detallado de vueltas

### 🔄 Tablas Mejoradas

-   **usuarios** - Añadido rol "juez" y teléfono
-   **pilotos** - Añadido tipo_licencia, foto_url, relación con país
-   **carreras** - Múltiples campos nuevos (premios, vueltas, streaming, clima)
-   **compras** - Añadido vehículo, comprobante, número de competidor
-   **resultados** - Muy ampliado (velocidades, sectores, penalizaciones)
-   **entregas** - Mejorado con empresa transporte, firma digital, ciudad

### 📊 Vistas SQL Nuevas

-   ✅ **v_ranking_pilotos** - Ranking general con estadísticas
-   ✅ **v_estadisticas_vueltas** - Análisis de rendimiento

---

## 🔗 Relaciones Clave

```
Estructura Jerárquica:

pais → city → {country, storage_id, pilotos, entregas}
brand → vehicle ← type
categorias → carreras ← country
pilotos ← compras → carreras
        ↓
    {bank, entregas, resultados}
        ↓
      lap
```

### Relaciones Importantes

-   **N:M entre pilotos y carreras** vía tabla `compras`
-   **1:1 entre compras y entregas**
-   **1:1 entre compras y resultados**
-   **1:N entre resultados y lap** (múltiples vueltas)
-   **Circuitos vinculados a ciudades** para geolocalización

---

## 📊 Datos de Ejemplo Incluidos

| Tabla      | Registros        |
| ---------- | ---------------- |
| pais       | 8                |
| usuarios   | 3                |
| brand      | 6                |
| type       | 6                |
| vehicle    | 5                |
| categorias | 5                |
| city       | 8                |
| storage_id | 3                |
| country    | 5                |
| pilotos    | 5                |
| carreras   | 4                |
| compras    | 5                |
| bank       | 3                |
| entregas   | 3                |
| resultados | 3                |
| lap        | 5                |
| **TOTAL**  | **76 registros** |

---

## 🚀 Características Avanzadas

### 1. **Geolocalización Completa**

-   Países con códigos ISO
-   Ciudades con coordenadas GPS (latitud/longitud)
-   Circuitos con características técnicas detalladas

### 2. **Gestión de Vehículos**

-   Registro completo de vehículos
-   Vinculación con marcas y tipos
-   Especificaciones técnicas en JSON
-   Número de chasis único

### 3. **Sistema de Resultados Avanzado**

-   Posición de salida y llegada
-   Tiempos por sector
-   Velocidades promedio y máximas
-   Registro de vueltas individuales
-   Mejor vuelta de la carrera

### 4. **Información Financiera**

-   Múltiples cuentas bancarias por piloto
-   Soporte para diferentes tipos (banco, PayPal, MercadoPago)
-   CBU/CVU y alias
-   Cuenta principal designada

### 5. **Logística de Entregas Mejorada**

-   Vinculación con ciudades (normalizado)
-   Múltiples estados de seguimiento
-   Tracking number
-   Firma digital del receptor
-   Costo de envío

---

## 🔐 Seguridad Implementada

### Contraseñas

-   ✅ Hash con `password_hash()` de PHP
-   ✅ Algoritmo bcrypt por defecto
-   ✅ Nunca en texto plano

### Integridad de Datos

-   ✅ Foreign Keys con restricciones apropiadas
-   ✅ UNIQUE constraints en campos críticos
-   ✅ ENUM para validar valores específicos
-   ✅ Índices para optimizar consultas

### Validaciones

-   ✅ NOT NULL en campos obligatorios
-   ✅ ON DELETE CASCADE/RESTRICT/SET NULL según lógica
-   ✅ Constraints de unicidad (piloto no puede inscribirse 2 veces)

---

## 📈 Optimizaciones

### Índices Estratégicos

-   ✅ Índices en Foreign Keys
-   ✅ Índices en campos de búsqueda frecuente (email, licencia, tracking)
-   ✅ Índices en campos de filtrado (estado, fecha, rol)
-   ✅ Índices compuestos donde sea necesario

### Vistas SQL Optimizadas

Todas las vistas utilizan JOINs optimizados con los índices definidos:

-   `v_carreras_completas`
-   `v_compras_completas`
-   `v_entregas_completas`
-   `v_resultados_completos`
-   `v_ranking_pilotos`
-   `v_estadisticas_vueltas`

---

## 🎓 Cumplimiento de Requisitos

### ✅ Requisitos del Proyecto

#### Base de Datos

-   [x] Tablas en MySQL con relaciones
-   [x] Foreign Keys correctamente definidas
-   [x] Índices para optimización
-   [x] Vistas SQL para consultas complejas
-   [x] Datos de ejemplo para testing

#### Seguridad

-   [x] Contraseñas hasheadas
-   [x] Tabla de usuarios para backend
-   [x] Roles definidos (admin, gestor, supervisor, juez)
-   [x] Preparado para sanitización con mysqli

#### Estructura para el Proyecto

-   [x] Tabla de productos (carreras)
-   [x] Tabla de clientes (pilotos) con Firebase UID
-   [x] Tabla de compras/inscripciones
-   [x] Tabla de entregas con gestión
-   [x] Sistema de resultados y ranking

---

## 🔄 Flujo del Sistema

### Frontend (Vue.js)

```
1. Piloto se registra → Firebase Auth → pilotos (firebase_uid)
2. Ve carreras disponibles → v_carreras_completas
3. Selecciona carrera + vehículo → compras
4. Realiza pago → UPDATE estado_pago
5. Sistema crea entrega → entregas
```

### Backend (PHP)

```
1. Gestor se loguea → usuarios (con CAPTCHA)
2. Ve entregas pendientes → v_entregas_completas
3. Asigna tracking → UPDATE entregas
4. Gestiona envíos → UPDATE estados
```

### Resultados

```
1. Carrera finaliza → INSERT resultados
2. Vueltas registradas → INSERT lap
3. Ranking actualizado → v_ranking_pilotos
4. Público ve resultados → v_resultados_completos
```

---

## 📝 Próximos Pasos

### Paso 1: Importar Base de Datos ✅

```bash
# Iniciar XAMPP
# Abrir phpMyAdmin: http://localhost/phpmyadmin
# Importar: database/podium_db.sql
```

### Paso 2: Crear Backend PHP ⏳

-   Carpeta `backend/`
-   Archivo `config/conexion.php`
-   Terminal `Terminal_carga_productos.php`
-   CRUDs necesarios
-   Sistema de login con CAPTCHA

### Paso 3: Integrar Firebase ⏳

-   Configurar proyecto Firebase
-   Obtener credenciales
-   Implementar en Vue.js
-   Sincronizar con MySQL

### Paso 4: Conectar Frontend ⏳

-   Consumir APIs desde Vue.js
-   Formularios de inscripción
-   Modal de login con Bootstrap
-   Visualización de resultados

---

## 🔍 Testing Inicial

### Consultas de Prueba

```sql
-- Ver todas las carreras disponibles
SELECT * FROM v_carreras_completas
WHERE estado = 'inscripciones_abiertas';

-- Ver inscripciones de un piloto
SELECT * FROM v_compras_completas
WHERE email_piloto = 'carlos.rodriguez@email.com';

-- Ver entregas pendientes
SELECT * FROM v_entregas_completas
WHERE estado_entrega = 'pendiente';

-- Ver ranking de pilotos
SELECT * FROM v_ranking_pilotos
ORDER BY puntos_totales DESC;

-- Ver resultados de una carrera
SELECT * FROM v_resultados_completos
WHERE id_carrera = 1
ORDER BY posicion_final;
```

---

## 📞 Credenciales de Acceso

### Backend (Gestión)

| Rol    | Email             | Password |
| ------ | ----------------- | -------- |
| Admin  | admin@podium.com  | admin123 |
| Gestor | gestor@podium.com | admin123 |
| Juez   | juez@podium.com   | admin123 |

⚠️ **IMPORTANTE:** Cambiar estas contraseñas en producción

---

## 📚 Documentación

-   **README_DB.md** - Documentación completa de cada tabla
-   **ER_DIAGRAM.md** - Diagrama ER visual y relaciones
-   **podium_db.sql** - Script SQL completo y ejecutable

---

## 🎉 Conclusión

### ✅ Base de Datos Completa y Profesional

-   16 tablas normalizadas
-   6 vistas SQL optimizadas
-   76 registros de ejemplo
-   Relaciones bien definidas
-   Índices estratégicos
-   Seguridad implementada

### 🚀 Lista para Desarrollo

La base de datos está completamente lista para:

-   Importación en XAMPP/MySQL
-   Desarrollo del backend PHP
-   Integración con Vue.js
-   Integración con Firebase
-   Testing y producción

---

**Sistema:** PODIUM Racing Management  
**Versión DB:** 2.0  
**Fecha:** Noviembre 2025  
**Estado:** ✅ Listo para implementación
