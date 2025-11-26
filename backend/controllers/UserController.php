<?php
// Controlador de usuarios

class UserController {
    private $db;
    private $auth;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->auth = new AuthMiddleware();
    }
    
    /**
     * Crear o actualizar usuario después del registro en Firebase
     */
    public function createOrUpdate() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['firebase_uid']) || empty($data['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'firebase_uid y email son requeridos']);
            return;
        }
        
        try {
            // Verificar si el usuario ya existe
            $stmt = $this->db->prepare("SELECT * FROM users WHERE firebase_uid = ?");
            $stmt->execute([$data['firebase_uid']]);
            $existingUser = $stmt->fetch();
            
            if ($existingUser) {
                // Actualizar usuario existente
                $stmt = $this->db->prepare("UPDATE users SET email = ?, updated_at = NOW() WHERE firebase_uid = ?");
                $stmt->execute([$data['email'], $data['firebase_uid']]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Usuario actualizado',
                    'user' => [
                        'id' => $existingUser['id'],
                        'email' => $data['email'],
                        'role' => $existingUser['role']
                    ]
                ]);
            } else {
                // Crear nuevo usuario (por defecto rol 'user')
                $role = $data['role'] ?? 'user';
                $stmt = $this->db->prepare("INSERT INTO users (firebase_uid, email, role) VALUES (?, ?, ?)");
                $stmt->execute([$data['firebase_uid'], $data['email'], $role]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Usuario creado',
                    'user' => [
                        'id' => $this->db->lastInsertId(),
                        'email' => $data['email'],
                        'role' => $role
                    ]
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al procesar usuario: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Obtener información del usuario actual
     */
    public function getCurrentUser() {
        $user = $this->auth->requireAuth();
        
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'],
                'created_at' => $user['created_at']
            ]
        ]);
    }
    
    /**
     * Listar todos los usuarios (solo admin)
     */
    public function listAll() {
        $this->auth->requireAdmin();
        
        $stmt = $this->db->query("SELECT id, email, role, created_at FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'users' => $users
        ]);
    }
    
    /**
     * Actualizar rol de usuario (solo admin)
     */
    public function updateRole($id) {
        $this->auth->requireAdmin();
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['role']) || !in_array($data['role'], ['admin', 'user'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Rol inválido']);
            return;
        }
        
        try {
            $stmt = $this->db->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$data['role'], $id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Rol actualizado correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar rol']);
        }
    }
}

