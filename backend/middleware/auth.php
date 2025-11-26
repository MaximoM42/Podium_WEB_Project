<?php
// Middleware de autenticación

class AuthMiddleware {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Verifica el token de Firebase del header Authorization
     * Retorna los datos del usuario o false si no está autenticado
     */
    public function authenticate() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        if (empty($authHeader)) {
            return false;
        }
        
        // Extraer el token Bearer
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $firebaseUid = $matches[1];
            
            // Buscar usuario en la base de datos por firebase_uid
            $stmt = $this->db->prepare("SELECT * FROM users WHERE firebase_uid = ?");
            $stmt->execute([$firebaseUid]);
            $user = $stmt->fetch();
            
            if ($user) {
                return $user;
            }
        }
        
        return false;
    }
    
    /**
     * Verifica si el usuario es administrador
     */
    public function isAdmin($user) {
        return $user && $user['role'] === 'admin';
    }
    
    /**
     * Requiere autenticación - responde con error 401 si no está autenticado
     */
    public function requireAuth() {
        $user = $this->authenticate();
        if (!$user) {
            header('HTTP/1.1 401 Unauthorized');
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado', 'message' => 'Se requiere autenticación']);
            exit();
        }
        return $user;
    }
    
    /**
     * Requiere rol de administrador - responde con error 403 si no es admin
     */
    public function requireAdmin() {
        $user = $this->requireAuth();
        if (!$this->isAdmin($user)) {
            header('HTTP/1.1 403 Forbidden');
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado', 'message' => 'Se requieren permisos de administrador']);
            exit();
        }
        return $user;
    }
}

