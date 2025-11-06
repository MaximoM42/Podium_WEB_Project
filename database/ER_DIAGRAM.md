# Diagrama Entidad-Relación - PODIUM DB v2.0

## 📊 Diagrama Completo

```
┌──────────┐
│   pais   │
│──────────│
│ id_pais  │◄────────┐
│ nombre   │         │
│codigo_iso│         │
└──────────┘         │
                     │ N:1
           ┌─────────┴────────┐      ┌──────────────┐
           │      city        │      │  storage_id  │
           │──────────────────│      │──────────────│
           │ id_city       PK │◄─────│ id_storage   │
           │ nombre           │  1:N │ nombre       │
           │ id_pais       FK │      │ id_city   FK │
           │ codigo_postal    │      │ capacidad    │
           │ latitud/longitud │      │ tipo         │
           └────────┬─────────┘      └──────────────┘
                    │
         ┌──────────┼────────────┐
         │          │            │
         │ 1:N      │ 1:N        │ 1:N
         │          │            │
  ┌──────▼──┐  ┌───▼──────┐  ┌──▼────────┐
  │ country │  │ pilotos  │  │ entregas  │
  │(circuito)│  │──────────│  │───────────│
  │─────────│  │id_piloto │  │id_entrega │
  │id_country│  │firebase_u│  │id_compra  │
  │nombre   │  │nombre    │  │id_city    │
  │id_city  │  │apellido  │  │direccion  │
  │longitud │  │email     │  │estado     │
  │n_curvas │  │id_pais   │  │tracking_n │
  │tipo     │  │n_licencia│  └───────────┘
  └────┬────┘  │tipo_lic  │
       │       │foto_url  │
       │       └─────┬────┘
       │             │
       │ 1:N         │ 1:N
       │             │
       │      ┌──────▼──────────┐
       │      │      bank       │
       │      │─────────────────│
       │      │ id_bank      PK │
       │      │ id_piloto    FK │
       │      │ nombre_banco    │
       │      │ tipo_cuenta     │
       │      │ cbu_cvu         │
       │      └─────────────────┘
       │
┌──────┴────────┐
│  categorias   │
│───────────────│
│ id_categoria  │
│ nombre        │
│ descripcion   │
│ reglamento    │
└──────┬────────┘
       │ 1:N
       │
┌──────▼────────┐
│   carreras    │
│───────────────│
│ id_carrera PK │◄──────────────────┐
│ nombre        │                   │
│ id_categoria  │                   │
│ id_country FK │                   │
│ fecha_inicio  │                   │
│ fecha_fin     │                   │
│ precio_insc.  │                   │
│ cupo_maximo   │                   │
│ n_vueltas     │                   │
│ premios       │                   │
│ estado        │                   │
└───────┬───────┘                   │
        │                           │
        │ N                         │
        │          ┌────────────────┘
        │          │ N
        │   ┌──────▼──────────┐
        └───►    compras      │
            │──────────────────│
            │ id_compra     PK │
            │ id_piloto     FK │───┐
            │ id_carrera    FK │   │
            │ id_vehicle    FK │   │ 1:1
            │ numero_compet    │   │
            │ monto            │   │
            │ estado_pago      │   │
            │ metodo_pago      │   │
            └──────┬───────────┘   │
                   │               │
         ┌─────────┼───────────────┘
         │ 1:1     │ 1:1
         │         │
    ┌────▼───┐  ┌─▼──────────┐
    │entregas│  │ resultados │
    └────────┘  │────────────│
                │id_resultado│
                │id_carrera  │
                │id_piloto   │
                │id_vehicle  │
                │pos_final   │
                │pos_salida  │
                │tiempo_total│
                │mejor_vuelta│
                │puntos      │
                │velocidades │
                │estado      │
                └─────┬──────┘
                      │
                      │ 1:N
                      │
                ┌─────▼──────┐
                │    lap     │
                │────────────│
                │ id_lap  PK │
                │id_resultado│
                │numero_vuelt│
                │tiempo_vuelt│
                │sector_1/2/3│
                │posicion    │
                └────────────┘


┌──────────┐          ┌───────────┐
│  brand   │          │   type    │
│──────────│          │───────────│
│ id_brand │          │ id_type   │
│ nombre   │          │ nombre    │
│logo_url  │          │descripcion│
└────┬─────┘          └─────┬─────┘
     │ 1:N                  │ 1:N
     └──────┬───────────────┘
            │
      ┌─────▼──────┐
      │  vehicle   │
      │────────────│
      │ id_vehicle │
      │ id_brand   │
      │ id_type    │
      │ modelo     │
      │ año        │
      │ n_chasis   │
      │ color      │
      │ especif.   │
      └────────────┘


┌──────────────┐
│   usuarios   │
│──────────────│
│ id_usuario   │
│ nombre       │
│ email        │
│ password     │
│ rol          │
└──────┬───────┘
       │
       │ 1:N (gestor de entregas)
       │
       └──────► entregas (id_usuario_gestor)
```

---

## 🔗 Relaciones Detalladas

### **Relaciones Geográficas**

#### `pais` → `city` (1:N)

-   Un país tiene muchas ciudades
-   Una ciudad pertenece a un país
-   **ON DELETE RESTRICT** (no borrar países con ciudades)

#### `city` → `country` (1:N) - Circuitos

-   Una ciudad puede tener múltiples circuitos
-   Un circuito está en una ciudad
-   **ON DELETE RESTRICT**

#### `city` → `storage_id` (1:N) - Almacenamientos

-   Una ciudad tiene varios lugares de almacenamiento
-   Un almacenamiento está en una ciudad
-   **ON DELETE RESTRICT**

#### `city` → `pilotos` (1:N)

-   Una ciudad/país tiene muchos pilotos
-   Un piloto es de un país
-   **ON DELETE SET NULL** (mantener piloto si se borra país)

#### `city` → `entregas` (1:N)

-   Una ciudad recibe muchas entregas
-   Una entrega va a una ciudad
-   **ON DELETE RESTRICT**

---

### **Relaciones de Vehículos**

#### `brand` → `vehicle` (1:N)

-   Una marca tiene muchos vehículos
-   Un vehículo es de una marca
-   **ON DELETE RESTRICT**

#### `type` → `vehicle` (1:N)

-   Un tipo tiene muchos vehículos
-   Un vehículo es de un tipo
-   **ON DELETE RESTRICT**

#### `vehicle` → `compras` (1:N)

-   Un vehículo puede usarse en varias inscripciones
-   Una inscripción usa un vehículo
-   **ON DELETE SET NULL** (opcional)

#### `vehicle` → `resultados` (1:N)

-   Un vehículo tiene varios resultados
-   Un resultado usa un vehículo
-   **ON DELETE SET NULL**

---

### **Relaciones de Carreras**

#### `categorias` → `carreras` (1:N)

-   Una categoría tiene muchas carreras
-   Una carrera es de una categoría
-   **ON DELETE RESTRICT**

#### `country` → `carreras` (1:N)

-   Un circuito alberga muchas carreras
-   Una carrera se corre en un circuito
-   **ON DELETE RESTRICT**

#### `carreras` ← `compras` → `pilotos` (N:M)

-   Relación muchos a muchos
-   Tabla intermedia: `compras`
-   **UNIQUE constraint:** (id_piloto, id_carrera)
-   Un piloto no puede inscribirse dos veces a la misma carrera

---

### **Relaciones de Compras/Inscripciones**

#### `compras` → `entregas` (1:1)

-   Cada compra tiene una entrega
-   Una entrega pertenece a una compra
-   **ON DELETE CASCADE**

#### `compras` → `resultados` (1:1)

-   Cada compra genera un resultado (si participa)
-   Un resultado es de una inscripción
-   **ON DELETE CASCADE**

---

### **Relaciones de Resultados**

#### `resultados` → `lap` (1:N)

-   Un resultado tiene muchas vueltas
-   Una vuelta pertenece a un resultado
-   **ON DELETE CASCADE**

---

### **Relaciones de Usuarios/Gestión**

#### `usuarios` → `entregas` (1:N)

-   Un gestor maneja muchas entregas
-   Una entrega tiene un gestor asignado
-   **ON DELETE SET NULL** (opcional)

#### `pilotos` → `bank` (1:N)

-   Un piloto tiene varias cuentas bancarias
-   Una cuenta pertenece a un piloto
-   **ON DELETE CASCADE**

---

## 📋 Constraints Importantes

### UNIQUE Constraints

| Tabla      | Campo                                | Descripción                     |
| ---------- | ------------------------------------ | ------------------------------- |
| pais       | nombre, codigo_iso                   | No duplicar países              |
| usuarios   | email                                | No duplicar emails              |
| brand      | nombre                               | Marcas únicas                   |
| categorias | nombre                               | Categorías únicas               |
| pilotos    | firebase_uid, email, numero_licencia | No duplicar pilotos             |
| vehicle    | numero_chasis                        | Chasis únicos                   |
| compras    | (id_piloto, id_carrera)              | No duplicar inscripciones       |
| resultados | (id_carrera, id_piloto)              | Un resultado por piloto/carrera |

### ENUM Constraints

| Tabla      | Campo         | Valores                                                             |
| ---------- | ------------- | ------------------------------------------------------------------- |
| usuarios   | rol           | admin, gestor_entregas, supervisor, juez                            |
| pilotos    | tipo_licencia | amateur, profesional, internacional                                 |
| storage_id | tipo          | garage, deposito, taller, paddock                                   |
| country    | tipo_pista    | circuito, calle, oval, mixto                                        |
| carreras   | estado        | programada, inscripciones_abiertas, en_curso, finalizada, cancelada |
| compras    | estado_pago   | pendiente, completado, reembolsado, cancelado                       |
| bank       | tipo_cuenta   | ahorro, corriente, paypal, mercadopago                              |
| resultados | estado        | finalizado, abandonado, descalificado, dns                          |
| entregas   | estado        | pendiente, preparando, en_camino, entregado, devuelto, cancelado    |

---

## 🔍 Índices de Optimización

### Índices por Tabla

**pais:** codigo_iso  
**usuarios:** email, rol  
**brand:** nombre  
**type:** nombre  
**vehicle:** id_brand, id_type, modelo  
**categorias:** nombre  
**city:** nombre, id_pais  
**storage_id:** id_city, tipo  
**country:** nombre, id_city  
**pilotos:** firebase_uid, email, id_pais, numero_licencia  
**carreras:** fecha_inicio, estado, id_categoria, id_country  
**compras:** id_piloto, id_carrera, id_vehicle, fecha_compra, estado_pago  
**bank:** id_piloto  
**resultados:** id_carrera, id_piloto, posicion_final  
**lap:** id_resultado, numero_vuelta  
**entregas:** id_compra, id_city, estado, id_usuario_gestor, tracking_number

---

## 📊 Flujo de Datos Principal

### 1. REGISTRO DE PILOTO

```
Firebase Auth → pilotos (firebase_uid) → bank (opcional)
```

### 2. INSCRIPCIÓN A CARRERA

```
Frontend Vue.js
    ↓
Selecciona: carreras (disponibles)
    ↓
Elige: vehicle (opcional)
    ↓
POST → Terminal_carga_productos.php
    ↓
INSERT → compras (estado_pago: pendiente)
    ↓
UPDATE → compras (estado_pago: completado)
    ↓
INSERT → entregas (estado: pendiente)
```

### 3. GESTIÓN DE ENTREGAS

```
Backend PHP (gestor logueado)
    ↓
SELECT → v_entregas_completas (WHERE estado = 'pendiente')
    ↓
UPDATE → entregas (id_usuario_gestor, tracking_number)
    ↓
UPDATE → entregas (estado: 'en_camino')
    ↓
UPDATE → entregas (estado: 'entregado', fecha_entrega)
```

### 4. CARRERA Y RESULTADOS

```
UPDATE → carreras (estado: 'en_curso')
    ↓
Durante carrera: INSERT → lap (tiempo por vuelta)
    ↓
Fin de carrera: INSERT → resultados
    ↓
UPDATE → carreras (estado: 'finalizada')
    ↓
Frontend: SELECT → v_resultados_completos
```

---

## 🎯 Vistas SQL y sus Usos

### `v_carreras_completas`

**Uso:** Mostrar carreras disponibles en el frontend

```sql
SELECT * FROM v_carreras_completas
WHERE estado = 'inscripciones_abiertas'
ORDER BY fecha_inicio;
```

### `v_compras_completas`

**Uso:** Historial de inscripciones del piloto

```sql
SELECT * FROM v_compras_completas
WHERE email_piloto = 'user@email.com';
```

### `v_entregas_completas`

**Uso:** Panel de gestión de entregas (backend)

```sql
SELECT * FROM v_entregas_completas
WHERE estado_entrega IN ('pendiente', 'preparando');
```

### `v_resultados_completos`

**Uso:** Tabla de posiciones de una carrera

```sql
SELECT * FROM v_resultados_completos
WHERE id_carrera = 1
ORDER BY posicion_final;
```

### `v_ranking_pilotos`

**Uso:** Clasificación general del campeonato

```sql
SELECT * FROM v_ranking_pilotos
ORDER BY puntos_totales DESC
LIMIT 10;
```

### `v_estadisticas_vueltas`

**Uso:** Análisis de rendimiento por vuelta

```sql
SELECT * FROM v_estadisticas_vueltas
WHERE id_resultado = 1;
```

---

## ⚡ Optimizaciones

### Consultas Frecuentes Optimizadas

#### 1. Carreras disponibles para inscripción

-   **Índice:** `carreras.estado`, `carreras.fecha_inicio`
-   **Vista:** `v_carreras_completas`

#### 2. Inscripciones de un piloto

-   **Índice:** `compras.id_piloto`, `compras.fecha_compra`
-   **Vista:** `v_compras_completas`

#### 3. Entregas pendientes

-   **Índice:** `entregas.estado`, `entregas.id_usuario_gestor`
-   **Vista:** `v_entregas_completas`

#### 4. Resultados de carrera

-   **Índice:** `resultados.id_carrera`, `resultados.posicion_final`
-   **Vista:** `v_resultados_completos`

#### 5. Tracking de entregas

-   **Índice:** `entregas.tracking_number`
-   **Consulta directa por tracking**

---

**Versión:** 2.0  
**Última actualización:** Noviembre 2025  
**Sistema:** PODIUM Racing Management
