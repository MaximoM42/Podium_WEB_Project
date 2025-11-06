# 🚀 Guía Rápida de Instalación - PODIUM DB

## ⚡ Instalación en 5 Pasos

---

## Paso 1: Iniciar XAMPP

### Windows

1. Abrir **XAMPP Control Panel**
2. Click en **Start** en Apache
3. Click en **Start** en MySQL
4. Esperar a que ambos estén en verde

✅ **Verificar:** Los módulos Apache y MySQL deben mostrar estado "Running"

---

## Paso 2: Abrir phpMyAdmin

1. Abrir navegador web
2. Ir a: **`http://localhost/phpmyadmin`**
3. Login automático (usuario: `root`, sin contraseña)

---

## Paso 3: Importar Base de Datos

### Método 1: Automático (Recomendado)

1. En phpMyAdmin, click en **"Importar"** (pestaña superior)
2. Click en **"Seleccionar archivo"**
3. Buscar el archivo:
    ```
    Podium_WEB_Project/database/podium_db.sql
    ```
4. Click en **"Continuar"** (botón inferior)
5. ✅ Esperar mensaje: "Importación finalizada con éxito"

### Método 2: Consola MySQL

Abrir PowerShell en la carpeta del proyecto:

```powershell
cd "c:\Users\ezequ\OneDrive\Desktop\Accesos Directos\Facu\Universidad\Cuatrimestre VIV\Programacion e Interfaces Visuales\Podium_WEB_Project"

# Ejecutar importación
& "C:\xampp\mysql\bin\mysql.exe" -u root < database\podium_db.sql
```

---

## Paso 4: Verificar Instalación

### En phpMyAdmin:

1. En el panel izquierdo, seleccionar base de datos **`podium_db`**

2. Verificar que existan **16 tablas**:

    - ✅ bank
    - ✅ brand
    - ✅ carreras
    - ✅ categorias
    - ✅ city
    - ✅ compras
    - ✅ country
    - ✅ entregas
    - ✅ lap
    - ✅ pais
    - ✅ pilotos
    - ✅ resultados
    - ✅ storage_id
    - ✅ type
    - ✅ usuarios
    - ✅ vehicle

3. Click en pestaña **"SQL"** y ejecutar:

```sql
-- Ver carreras disponibles
SELECT * FROM v_carreras_completas;

-- Ver usuarios del sistema
SELECT nombre, apellido, email, rol FROM usuarios;

-- Contar registros
SELECT 'pilotos' AS tabla, COUNT(*) AS total FROM pilotos
UNION ALL
SELECT 'carreras', COUNT(*) FROM carreras
UNION ALL
SELECT 'compras', COUNT(*) FROM compras;
```

✅ **Resultado esperado:**

-   4 carreras
-   3 usuarios
-   5 pilotos
-   5 compras

---

## Paso 5: Probar Consultas

### Consultas de Prueba en phpMyAdmin (SQL):

#### 1. Ver todas las carreras con detalles

```sql
SELECT
    nombre_carrera,
    categoria,
    circuito,
    ciudad,
    pais,
    fecha_inicio,
    inscriptos,
    cupos_disponibles
FROM v_carreras_completas
ORDER BY fecha_inicio;
```

#### 2. Ver inscripciones completas

```sql
SELECT
    piloto,
    carrera,
    numero_competidor,
    vehiculo,
    marca_vehiculo,
    estado_pago,
    monto
FROM v_compras_completas;
```

#### 3. Ver ranking de pilotos

```sql
SELECT
    piloto,
    pais,
    carreras_participadas,
    puntos_totales,
    victorias,
    podios
FROM v_ranking_pilotos
ORDER BY puntos_totales DESC;
```

#### 4. Ver entregas pendientes

```sql
SELECT
    piloto,
    carrera,
    ciudad,
    direccion_envio,
    tracking_number,
    estado_entrega
FROM v_entregas_completas
WHERE estado_entrega IN ('pendiente', 'preparando', 'en_camino');
```

---

## 🔐 Credenciales de Prueba

### Para Backend (PHP)

| Usuario | Email             | Password | Rol             |
| ------- | ----------------- | -------- | --------------- |
| Admin   | admin@podium.com  | admin123 | admin           |
| Gestor  | gestor@podium.com | admin123 | gestor_entregas |
| Juez    | juez@podium.com   | admin123 | juez            |

### Pilotos de Ejemplo (Frontend)

| Nombre           | Email                      | Licencia    |
| ---------------- | -------------------------- | ----------- |
| Carlos Rodríguez | carlos.rodriguez@email.com | LIC-ARG-001 |
| María González   | maria.gonzalez@email.com   | LIC-ARG-002 |
| Diego Martínez   | diego.martinez@email.com   | LIC-ARG-003 |

---

## ❌ Solución de Problemas

### Error: "Access denied for user 'root'@'localhost'"

**Solución 1: Resetear contraseña**

```sql
-- En phpMyAdmin, ejecutar:
ALTER USER 'root'@'localhost' IDENTIFIED BY '';
FLUSH PRIVILEGES;
```

**Solución 2: Verificar XAMPP**

-   Cerrar y reiniciar XAMPP
-   Verificar que MySQL esté en verde

---

### Error: "Database already exists"

**Solución:**

1. En phpMyAdmin, seleccionar `podium_db`
2. Click en **"Operaciones"** (pestaña superior)
3. Scroll down → **"Eliminar la base de datos (DROP)"**
4. Confirmar
5. Volver a importar `podium_db.sql`

---

### Error: "Cannot add foreign key constraint"

**Solución:**
Este error no debería aparecer si importas el archivo completo.
Si aparece, asegúrate de:

1. Importar el archivo **completo** `podium_db.sql`
2. No importar tablas por separado
3. El script crea las tablas en el orden correcto

---

### MySQL no inicia en XAMPP

**Solución 1: Liberar puerto 3306**

```powershell
# Ver qué programa usa el puerto 3306
netstat -ano | findstr :3306

# Detener el proceso (reemplazar PID)
taskkill /PID [numero_pid] /F
```

**Solución 2: Reparar archivos**

1. Cerrar XAMPP
2. Ir a `C:\xampp\mysql\data\`
3. Eliminar archivos:
    - `ib_logfile0`
    - `ib_logfile1`
4. Reiniciar XAMPP

---

### Error al importar: "Timeout" o "Script interrupted"

**Solución:**
El archivo es muy grande. Aumentar límites en phpMyAdmin:

1. Editar archivo: `C:\xampp\php\php.ini`
2. Buscar y modificar:
    ```ini
    upload_max_filesize = 128M
    post_max_size = 128M
    max_execution_time = 600
    max_input_time = 600
    ```
3. Reiniciar Apache en XAMPP
4. Volver a importar

---

## ✅ Checklist de Instalación

-   [ ] XAMPP instalado
-   [ ] Apache iniciado (verde)
-   [ ] MySQL iniciado (verde)
-   [ ] phpMyAdmin accesible en `http://localhost/phpmyadmin`
-   [ ] Archivo `podium_db.sql` ubicado
-   [ ] Base de datos importada exitosamente
-   [ ] 16 tablas visibles en phpMyAdmin
-   [ ] Consultas de prueba ejecutadas correctamente
-   [ ] Datos de ejemplo visibles

---

## 🎯 Próximos Pasos

### Una vez instalada la base de datos:

1. ✅ **Base de datos lista**
2. ⏭️ **Crear backend PHP**

    - Carpeta `backend/`
    - Configurar `conexion.php`
    - Crear `Terminal_carga_productos.php`
    - Implementar CRUDs

3. ⏭️ **Configurar Firebase**

    - Crear proyecto en Firebase Console
    - Obtener credenciales
    - Configurar en Vue.js

4. ⏭️ **Integrar Frontend**
    - Instalar dependencias en Vue.js
    - Conectar con backend PHP
    - Implementar vistas de carreras
    - Sistema de login

---

## 📚 Recursos Adicionales

-   **README_DB.md** - Documentación completa de tablas
-   **ER_DIAGRAM.md** - Diagrama de relaciones
-   **RESUMEN.md** - Resumen ejecutivo del proyecto

---

## 💡 Tips Útiles

### Ver tamaño de la base de datos

```sql
SELECT
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE table_schema = 'podium_db'
ORDER BY (data_length + index_length) DESC;
```

### Crear backup

```powershell
# En PowerShell
& "C:\xampp\mysql\bin\mysqldump.exe" -u root podium_db > backup_podium_$(Get-Date -Format "yyyyMMdd_HHmmss").sql
```

### Restaurar desde backup

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root podium_db < backup_podium_20251106_143000.sql
```

---

## 🆘 ¿Necesitas Ayuda?

Si encuentras algún problema:

1. ✅ Verifica que XAMPP esté corriendo
2. ✅ Revisa los logs en: `C:\xampp\mysql\data\mysql_error.log`
3. ✅ Consulta la documentación en `README_DB.md`
4. ✅ Verifica las relaciones en `ER_DIAGRAM.md`

---

**¡Listo para comenzar con el desarrollo! 🚀**

---

**Sistema:** PODIUM Racing Management  
**Versión:** 2.0  
**Fecha:** Noviembre 2025
