<?php
/**
 * PODIUM - Sistema de Gestión de Carreras
 * Archivo de Conexión a Base de Datos
 * 
 * Este archivo maneja la conexión a MySQL usando mysqli
 * con prepared statements para seguridad.
 */

// Prevenir acceso directo
if (!defined('PODIUM_ACCESS')) {
    http_response_code(403);
    die('Acceso denegado');
}

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'podium_db');
define('DB_CHARSET', 'utf8mb4');

// Clase de Conexión a la Base de Datos
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $charset = DB_CHARSET;
    private $conn = null;
    private $error = null;

    /**
     * Constructor - Establece la conexión automáticamente
     */
    public function __construct() {
        $this->connect();
    }

    /**
     * Conectar a la base de datos
     * 
     * @return mysqli|null Conexión mysqli o null si falla
     */
    private function connect() {
        try {
            // Crear conexión mysqli
            $this->conn = new mysqli(
                $this->host,
                $this->user,
                $this->pass,
                $this->dbname
            );

            // Verificar errores de conexión
            if ($this->conn->connect_error) {
                throw new Exception("Error de conexión: " . $this->conn->connect_error);
            }

            // Establecer charset
            if (!$this->conn->set_charset($this->charset)) {
                throw new Exception("Error al establecer charset: " . $this->conn->error);
            }

            return $this->conn;

        } catch (Exception $e) {
            $this->error = $e->getMessage();
            error_log($this->error);
            return null;
        }
    }

    /**
     * Obtener la conexión
     * 
     * @return mysqli|null
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Obtener el último error
     * 
     * @return string|null
     */
    public function getError() {
        return $this->error;
    }

    /**
     * Verificar si la conexión está activa
     * 
     * @return bool
     */
    public function isConnected() {
        return $this->conn !== null && $this->conn->ping();
    }

    /**
     * Cerrar la conexión
     */
    public function close() {
        if ($this->conn !== null) {
            $this->conn->close();
            $this->conn = null;
        }
    }

    /**
     * Ejecutar consulta preparada (SELECT)
     * 
     * @param string $sql Consulta SQL con placeholders
     * @param array $params Parámetros a bindear
     * @param string $types Tipos de datos (i, d, s, b)
     * @return array|false Resultado o false si falla
     */
    public function query($sql, $params = [], $types = '') {
        if (!$this->isConnected()) {
            return false;
        }

        try {
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->conn->error);
            }

            // Bindear parámetros si existen
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            // Ejecutar
            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar consulta: " . $stmt->error);
            }

            // Obtener resultado
            $result = $stmt->get_result();
            
            if ($result) {
                $data = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                return $data;
            }

            $stmt->close();
            return [];

        } catch (Exception $e) {
            $this->error = $e->getMessage();
            error_log($this->error);
            return false;
        }
    }

    /**
     * Ejecutar consulta preparada (INSERT, UPDATE, DELETE)
     * 
     * @param string $sql Consulta SQL con placeholders
     * @param array $params Parámetros a bindear
     * @param string $types Tipos de datos (i, d, s, b)
     * @return bool|int ID insertado (INSERT) o true/false
     */
    public function execute($sql, $params = [], $types = '') {
        if (!$this->isConnected()) {
            return false;
        }

        try {
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->conn->error);
            }

            // Bindear parámetros si existen
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            // Ejecutar
            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar consulta: " . $stmt->error);
            }

            // Para INSERT, devolver el ID insertado
            if (stripos(trim($sql), 'INSERT') === 0) {
                $insertId = $stmt->insert_id;
                $stmt->close();
                return $insertId;
            }

            // Para UPDATE/DELETE, devolver número de filas afectadas
            $affectedRows = $stmt->affected_rows;
            $stmt->close();
            return $affectedRows > 0;

        } catch (Exception $e) {
            $this->error = $e->getMessage();
            error_log($this->error);
            return false;
        }
    }

    /**
     * Iniciar transacción
     * 
     * @return bool
     */
    public function beginTransaction() {
        return $this->conn->begin_transaction();
    }

    /**
     * Confirmar transacción
     * 
     * @return bool
     */
    public function commit() {
        return $this->conn->commit();
    }

    /**
     * Revertir transacción
     * 
     * @return bool
     */
    public function rollback() {
        return $this->conn->rollback();
    }

    /**
     * Sanitizar entrada para prevenir XSS
     * 
     * @param string $data Dato a sanitizar
     * @return string Dato sanitizado
     */
    public function sanitize($data) {
        if ($this->conn === null) {
            return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
        }
        
        $data = trim($data);
        $data = strip_tags($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        $data = $this->conn->real_escape_string($data);
        
        return $data;
    }

    /**
     * Escapar string para SQL
     * 
     * @param string $string String a escapar
     * @return string String escapado
     */
    public function escape($string) {
        if ($this->conn === null) {
            return addslashes($string);
        }
        return $this->conn->real_escape_string($string);
    }

    /**
     * Destructor - Cerrar conexión
     */
    public function __destruct() {
        $this->close();
    }
}

// Función helper para obtener una instancia de la base de datos
function getDB() {
    static $db = null;
    if ($db === null) {
        $db = new Database();
    }
    return $db;
}

// Verificar conexión al cargar el archivo
$testDB = new Database();
if (!$testDB->isConnected()) {
    error_log("ADVERTENCIA: No se pudo conectar a la base de datos");
}
unset($testDB);

?>
