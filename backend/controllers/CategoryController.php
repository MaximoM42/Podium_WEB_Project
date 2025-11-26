<?php
// Controlador de categorías

class CategoryController {
    private $db;
    private $auth;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->auth = new AuthMiddleware();
    }
    
    /**
     * Obtener todas las categorías (público)
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY id");
        $categories = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'categories' => $categories
        ]);
    }
    
    /**
     * Obtener una categoría por ID (público)
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch();
        
        if (!$category) {
            http_response_code(404);
            echo json_encode(['error' => 'Categoría no encontrada']);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'category' => $category
        ]);
    }
    
    /**
     * Crear nueva categoría (solo admin)
     */
    public function create() {
        $this->auth->requireAdmin();
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['id']) || empty($data['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ID y nombre son requeridos']);
            return;
        }
        
        try {
            $stmt = $this->db->prepare("INSERT INTO categories (id, name, description, image_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $data['id'],
                $data['name'],
                $data['description'] ?? null,
                $data['image_url'] ?? null
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Categoría creada correctamente',
                'category' => $data
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                http_response_code(409);
                echo json_encode(['error' => 'Ya existe una categoría con ese ID']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al crear categoría']);
            }
        }
    }
    
    /**
     * Actualizar categoría (solo admin)
     */
    public function update($id) {
        $this->auth->requireAdmin();
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        try {
            $stmt = $this->db->prepare("UPDATE categories SET name = ?, description = ?, image_url = ? WHERE id = ?");
            $stmt->execute([
                $data['name'] ?? null,
                $data['description'] ?? null,
                $data['image_url'] ?? null,
                $id
            ]);
            
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Categoría no encontrada']);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Categoría actualizada correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar categoría']);
        }
    }
    
    /**
     * Eliminar categoría (solo admin)
     */
    public function delete($id) {
        $this->auth->requireAdmin();
        
        try {
            $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Categoría no encontrada']);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Categoría eliminada correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar categoría']);
        }
    }
}

