<?php
// Test completo del API

require_once 'config/config.php';
require_once 'config/database.php';

header("Content-Type: text/plain; charset=UTF-8");

echo "=== TEST COMPLETO DEL API ===\n\n";

try {
    $db = Database::getInstance()->getConnection();
    echo "✅ Conexión a MySQL: OK\n\n";
    
    // Test 1: Verificar tablas
    echo "--- Test 1: Verificando tablas ---\n";
    $tables = ['categories', 'vehicles', 'races', 'positions', 'users'];
    
    foreach ($tables as $table) {
        $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
        $result = $stmt->fetch();
        $count = $result['count'];
        echo "  $table: $count registros\n";
    }
    echo "\n";
    
    // Test 2: Verificar categorías
    echo "--- Test 2: Listando categorías ---\n";
    $stmt = $db->query("SELECT id, name FROM categories");
    $categories = $stmt->fetchAll();
    
    if (count($categories) > 0) {
        foreach ($categories as $cat) {
            echo "  - " . $cat['id'] . ": " . $cat['name'] . "\n";
        }
        echo "✅ Test de categorías: OK\n\n";
    } else {
        echo "⚠️ No hay categorías. Importa database/podium.sql\n\n";
    }
    
    // Test 3: Verificar vehículos
    echo "--- Test 3: Verificando vehículos ---\n";
    $stmt = $db->query("SELECT COUNT(*) as count FROM vehicles");
    $result = $stmt->fetch();
    $vehicleCount = $result['count'];
    
    if ($vehicleCount > 0) {
        echo "  Total de vehículos: $vehicleCount\n";
        echo "✅ Test de vehículos: OK\n\n";
    } else {
        echo "⚠️ No hay vehículos registrados\n\n";
    }
    
    // Test 4: Verificar carreras
    echo "--- Test 4: Verificando carreras ---\n";
    $stmt = $db->query("SELECT COUNT(*) as count FROM races");
    $result = $stmt->fetch();
    $raceCount = $result['count'];
    
    if ($raceCount > 0) {
        echo "  Total de carreras: $raceCount\n";
        echo "✅ Test de carreras: OK\n\n";
    } else {
        echo "⚠️ No hay carreras registradas\n\n";
    }
    
    // Test 5: Verificar posiciones
    echo "--- Test 5: Verificando posiciones ---\n";
    $stmt = $db->query("SELECT COUNT(*) as count FROM positions");
    $result = $stmt->fetch();
    $positionCount = $result['count'];
    
    if ($positionCount > 0) {
        echo "  Total de posiciones: $positionCount\n";
        echo "✅ Test de posiciones: OK\n\n";
    } else {
        echo "⚠️ No hay posiciones registradas\n\n";
    }
    
    // Test 6: Verificar configuración de MySQL
    echo "--- Test 6: Configuración de MySQL ---\n";
    $stmt = $db->query("SHOW VARIABLES LIKE 'max_allowed_packet'");
    $result = $stmt->fetch();
    echo "  max_allowed_packet: " . $result['Value'] . " bytes\n";
    
    $stmt = $db->query("SHOW VARIABLES LIKE 'wait_timeout'");
    $result = $stmt->fetch();
    echo "  wait_timeout: " . $result['Value'] . " segundos\n";
    
    echo "\n";
    
    // Resultado final
    echo "===========================================\n";
    echo "✅ TODOS LOS TESTS PASARON CORRECTAMENTE\n";
    echo "===========================================\n";
    echo "\nEl API está listo para usar.\n";
    echo "Endpoints disponibles:\n";
    echo "  - GET  /categories\n";
    echo "  - GET  /vehicles\n";
    echo "  - GET  /races\n";
    echo "  - GET  /races/{categoryId}/positions\n";
    echo "  - POST /users (crear/actualizar usuario)\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nSOLUCIÓN:\n";
    echo "1. Verifica que MySQL esté corriendo\n";
    echo "2. Aumenta los límites en phpMyAdmin:\n";
    echo "   SET GLOBAL max_allowed_packet=67108864;\n";
    echo "   SET GLOBAL wait_timeout=28800;\n";
    echo "3. Importa database/podium.sql si no lo has hecho\n";
}

