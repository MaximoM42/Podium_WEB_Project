<?php
// Test simple de conexión sin usar clases

header("Content-Type: text/plain; charset=UTF-8");

echo "=== TEST SIMPLE DE CONEXIÓN ===\n\n";

// Configuración directa
$host = 'localhost';
$dbname = 'podium';
$user = 'root';  // Solo 'root', sin @localhost
$pass = '';

try {
    echo "1. Intentando conectar a MySQL...\n";
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "✅ Conexión exitosa\n\n";
    
    // Configurar charset
    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Charset configurado\n\n";
    
    // Probar consulta simple
    echo "2. Probando consulta a la tabla categories...\n";
    $stmt = $pdo->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll();
    
    if (count($categories) > 0) {
        echo "✅ Encontradas " . count($categories) . " categorías:\n";
        foreach ($categories as $cat) {
            echo "  - " . $cat['id'] . ": " . $cat['name'] . "\n";
        }
    } else {
        echo "⚠️ No hay categorías. Importa database/podium.sql\n";
    }
    
    echo "\n=== TEST EXITOSO ===\n";
    echo "Si ves esto, la conexión funciona correctamente.\n";
    
} catch (PDOException $e) {
    echo "❌ ERROR:\n";
    echo "Código: " . $e->getCode() . "\n";
    echo "Mensaje: " . $e->getMessage() . "\n\n";
    
    if ($e->getCode() == 2002) {
        echo "SOLUCIÓN: MySQL no está corriendo. Inícialo desde XAMPP.\n";
    } elseif ($e->getCode() == 1049) {
        echo "SOLUCIÓN: La base de datos 'podium' no existe. Créala e importa el SQL.\n";
    }
}

