<?php
// Archivo para verificar la configuración de PHP

echo "<h1>Información de PHP</h1>";

// Mostrar versión de PHP
echo "<h2>Versión de PHP: " . PHP_VERSION . "</h2>";

// Verificar extensiones PDO
echo "<h2>Extensiones PDO:</h2>";
if (extension_loaded('PDO')) {
    echo "✅ PDO está instalado<br>";
    
    $drivers = PDO::getAvailableDrivers();
    if (count($drivers) > 0) {
        echo "Drivers disponibles:<br>";
        foreach ($drivers as $driver) {
            echo "  - " . $driver . "<br>";
        }
    } else {
        echo "❌ No hay drivers PDO disponibles<br>";
    }
} else {
    echo "❌ PDO NO está instalado<br>";
}

// Verificar extensión MySQL
echo "<h2>Extensión MySQL:</h2>";
if (extension_loaded('mysqli')) {
    echo "✅ MySQLi está instalado<br>";
} else {
    echo "❌ MySQLi NO está instalado<br>";
}

if (extension_loaded('pdo_mysql')) {
    echo "✅ PDO_MySQL está instalado<br>";
} else {
    echo "❌ PDO_MySQL NO está instalado<br>";
}

// Mostrar archivo php.ini que se está usando
echo "<h2>Archivo php.ini:</h2>";
echo php_ini_loaded_file() . "<br>";

echo "<hr>";
echo "<h3>Para ver toda la configuración de PHP, descomenta la siguiente línea:</h3>";
// phpinfo(); // Descomenta esta línea si necesitas ver todo
?>

