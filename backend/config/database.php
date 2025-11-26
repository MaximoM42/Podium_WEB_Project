<?php
// Clase de conexión a la base de datos con reconexión automática

class Database {
    private $connection;
    private static $instance = null;
    private $dsn;
    private $options;
    
    private function __construct() {
        // Asegurar que las constantes estén definidas
        if (!defined('DB_HOST')) {
            require_once __DIR__ . '/config.php';
        }
        
        $this->dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $this->options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false, // Evitar conexiones persistentes problemáticas
        ];
        
        $this->connect();
    }
    
    private function connect() {
        try {
            $this->connection = new PDO($this->dsn, DB_USER, DB_PASS, $this->options);
            // Configurar charset después de la conexión (compatible con todas las versiones)
            $this->connection->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        // Verificar si la conexión sigue viva
        try {
            $this->connection->query('SELECT 1');
        } catch (PDOException $e) {
            // Si falla, reconectar
            error_log("Connection lost, reconnecting...");
            $this->connect();
        }
        
        return $this->connection;
    }
    
    // Prevenir clonación
    private function __clone() {}
    
    // Prevenir deserialización
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

