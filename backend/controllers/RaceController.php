<?php
// Controlador de carreras

class RaceController {
    private $db;
    private $auth;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->auth = new AuthMiddleware();
    }
    
    /**
     * Obtener todas las carreras (público)
     */
    public function getAll() {
        $categoryId = $_GET['category_id'] ?? null;
        
        if ($categoryId) {
            $stmt = $this->db->prepare("SELECT * FROM races WHERE category_id = ? ORDER BY date DESC");
            $stmt->execute([$categoryId]);
        } else {
            $stmt = $this->db->query("SELECT * FROM races ORDER BY date DESC");
        }
        
        $races = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'races' => $races
        ]);
    }
    
    /**
     * Obtener una carrera por ID con sus posiciones (público)
     */
    public function getById($id) {
        // Obtener carrera
        $stmt = $this->db->prepare("SELECT * FROM races WHERE id = ?");
        $stmt->execute([$id]);
        $race = $stmt->fetch();
        
        if (!$race) {
            http_response_code(404);
            echo json_encode(['error' => 'Carrera no encontrada']);
            return;
        }
        
        // Obtener posiciones con información del vehículo
        $stmt = $this->db->prepare("
            SELECT p.*, v.name as vehicle_name, v.brand, v.model 
            FROM positions p
            LEFT JOIN vehicles v ON p.vehicle_id = v.id
            WHERE p.race_id = ?
            ORDER BY p.position ASC
        ");
        $stmt->execute([$id]);
        $positions = $stmt->fetchAll();
        
        $race['positions'] = $positions;
        
        echo json_encode([
            'success' => true,
            'race' => $race
        ]);
    }
    
    /**
     * Obtener carreras de una categoría con sus posiciones (público)
     */
    public function getByCategoryWithPositions($categoryId) {
        // Obtener carreras
        $stmt = $this->db->prepare("SELECT * FROM races WHERE category_id = ? ORDER BY date DESC");
        $stmt->execute([$categoryId]);
        $races = $stmt->fetchAll();
        
        // Para cada carrera, obtener sus posiciones
        foreach ($races as &$race) {
            $stmt = $this->db->prepare("
                SELECT p.*, v.name as vehicle_name, v.brand, v.model 
                FROM positions p
                LEFT JOIN vehicles v ON p.vehicle_id = v.id
                WHERE p.race_id = ?
                ORDER BY p.position ASC
            ");
            $stmt->execute([$race['id']]);
            $race['positions'] = $stmt->fetchAll();
        }
        
        echo json_encode([
            'success' => true,
            'races' => $races
        ]);
    }
    
    /**
     * Crear nueva carrera (solo admin)
     */
    public function create() {
        $this->auth->requireAdmin();
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['category_id']) || empty($data['name']) || empty($data['date'])) {
            http_response_code(400);
            echo json_encode(['error' => 'category_id, name y date son requeridos']);
            return;
        }
        
        try {
            $stmt = $this->db->prepare("INSERT INTO races (category_id, name, location, date, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['category_id'],
                $data['name'],
                $data['location'] ?? null,
                $data['date'],
                $data['status'] ?? 'scheduled'
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Carrera creada correctamente',
                'race_id' => $this->db->lastInsertId()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear carrera: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Actualizar carrera (solo admin)
     */
    public function update($id) {
        $this->auth->requireAdmin();
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        try {
            $stmt = $this->db->prepare("UPDATE races SET category_id = ?, name = ?, location = ?, date = ?, status = ? WHERE id = ?");
            $stmt->execute([
                $data['category_id'] ?? null,
                $data['name'] ?? null,
                $data['location'] ?? null,
                $data['date'] ?? null,
                $data['status'] ?? 'scheduled',
                $id
            ]);
            
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Carrera no encontrada']);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Carrera actualizada correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar carrera']);
        }
    }
    
    /**
     * Eliminar carrera (solo admin)
     */
    public function delete($id) {
        $this->auth->requireAdmin();
        
        try {
            $stmt = $this->db->prepare("DELETE FROM races WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Carrera no encontrada']);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Carrera eliminada correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar carrera']);
        }
    }
}

