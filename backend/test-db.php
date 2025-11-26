<?php
// Archivo de prueba de conexión a la base de datos

header("Content-Type: application/json; charset=UTF-8");

echo "=== PRUEBA DE CONEXIÓN A MYSQL ===\n\n";

// Configuración
$host = 'localhost';
$dbname = 'podium';
$user = 'root';
$pass = '';

echo "Configuración:\n";
echo "- Host: $host\n";
echo "- Base de datos: $dbname\n";
echo "- Usuario: $user\n";
echo "- Password: " . (empty($pass) ? "(vacío)" : "(configurado)") . "\n\n";

// Intentar conectar
try {
    echo "Intentando conectar a MySQL...\n";
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    echo "✅ Conexión al servidor MySQL: EXITOSA\n\n";
    
    // Verificar si la base de datos existe
    echo "Verificando si la base de datos '$dbname' existe...\n";
    $stmt = $pdo->query("SHOW DATABASES LIKE '$dbname'");
    $exists = $stmt->fetch();
    
    if ($exists) {
        echo "✅ Base de datos '$dbname': EXISTE\n\n";
        
        // Conectar a la base de datos específica
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
        echo "✅ Conexión a la base de datos '$dbname': EXITOSA\n\n";
        
        // Verificar tablas
        echo "Tablas en la base de datos:\n";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            foreach ($tables as $table) {
                $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                echo "  - $table ($count registros)\n";
            }
        } else {
            echo "  ⚠️ No hay tablas en la base de datos\n";
            echo "  ℹ️ Necesitas importar database/podium.sql\n";
        }
        
        echo "\n✅ TODO ESTÁ CORRECTO - La base de datos está funcionando\n";
        
    } else {
        echo "❌ Base de datos '$dbname': NO EXISTE\n\n";
        echo "Bases de datos disponibles:\n";
        $stmt = $pdo->query("SHOW DATABASES");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo "  - " . $row[0] . "\n";
        }
        echo "\n";
        echo "SOLUCIÓN: Debes crear la base de datos 'podium' e importar el archivo SQL\n";
    }
    
} catch (PDOException $e) {
    echo "❌ ERROR DE CONEXIÓN\n\n";
    echo "Código de error: " . $e->getCode() . "\n";
    echo "Mensaje: " . $e->getMessage() . "\n\n";
    
    // Diagnósticos específicos
    if ($e->getCode() == 2002 || strpos($e->getMessage(), 'Connection refused') !== false) {
        echo "DIAGNÓSTICO: MySQL no está corriendo\n";
        echo "SOLUCIÓN: Inicia MySQL desde el panel de XAMPP\n";
    } elseif ($e->getCode() == 1045) {
        echo "DIAGNÓSTICO: Usuario o contraseña incorrectos\n";
        echo "SOLUCIÓN: Verifica las credenciales en backend/config/config.php\n";
    } elseif ($e->getCode() == 1049) {
        echo "DIAGNÓSTICO: La base de datos no existe\n";
        echo "SOLUCIÓN: Crea la base de datos 'podium' e importa database/podium.sql\n";
    }
}

