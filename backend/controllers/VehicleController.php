<?php
// Controlador de vehículos

class VehicleController {
    private $db;
    private $auth;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->auth = new AuthMiddleware();
    }
    
    /**
     * Obtener todos los vehículos (público)
     */
    public function getAll() {
        $categoryId = $_GET['category_id'] ?? null;
        
        if ($categoryId) {
            $stmt = $this->db->prepare("SELECT * FROM vehicles WHERE category_id = ? ORDER BY name");
            $stmt->execute([$categoryId]);
        } else {
            $stmt = $this->db->query("SELECT * FROM vehicles ORDER BY name");
        }
        
        $vehicles = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'vehicles' => $vehicles
        ]);
    }
    
    /**
     * Obtener un vehículo por ID (público)
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM vehicles WHERE id = ?");
        $stmt->execute([$id]);
        $vehicle = $stmt->fetch();
        
        if (!$vehicle) {
            http_response_code(404);
            echo json_encode(['error' => 'Vehículo no encontrado']);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'vehicle' => $vehicle
        ]);
    }
    
    /**
     * Crear nuevo vehículo (solo admin)
     */
    public function create() {
        $this->auth->requireAdmin();
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'El nombre es requerido']);
            return;
        }
        
        try {
            $stmt = $this->db->prepare("INSERT INTO vehicles (name, brand, model, year, category_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['name'],
                $data['brand'] ?? null,
                $data['model'] ?? null,
                $data['year'] ?? null,
                $data['category_id'] ?? null
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Vehículo creado correctamente',
                'vehicle_id' => $this->db->lastInsertId()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear vehículo: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Actualizar vehículo (solo admin)
     */
    public function update($id) {
        $this->auth->requireAdmin();
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        try {
            $stmt = $this->db->prepare("UPDATE vehicles SET name = ?, brand = ?, model = ?, year = ?, category_id = ? WHERE id = ?");
            $stmt->execute([
                $data['name'] ?? null,
                $data['brand'] ?? null,
                $data['model'] ?? null,
                $data['year'] ?? null,
                $data['category_id'] ?? null,
                $id
            ]);
            
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Vehículo no encontrado']);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Vehículo actualizado correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar vehículo']);
        }
    }
    
    /**
     * Eliminar vehículo (solo admin)
     */
    public function delete($id) {
        $this->auth->requireAdmin();
        
        try {
            $stmt = $this->db->prepare("DELETE FROM vehicles WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Vehículo no encontrado']);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Vehículo eliminado correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar vehículo']);
        }
    }
}

