<?php
// Controlador de posiciones/resultados

class PositionController {
    private $db;
    private $auth;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->auth = new AuthMiddleware();
    }
    
    /**
     * Obtener posiciones de una carrera (público)
     */
    public function getByRace($raceId) {
        $stmt = $this->db->prepare("
            SELECT p.*, v.name as vehicle_name, v.brand, v.model 
            FROM positions p
            LEFT JOIN vehicles v ON p.vehicle_id = v.id
            WHERE p.race_id = ?
            ORDER BY p.position ASC
        ");
        $stmt->execute([$raceId]);
        $positions = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'positions' => $positions
        ]);
    }
    
    /**
     * Crear nueva posición (solo admin)
     */
    public function create() {
        $this->auth->requireAdmin();
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['race_id']) || empty($data['position']) || empty($data['driver_name'])) {
            http_response_code(400);
            echo json_encode(['error' => 'race_id, position y driver_name son requeridos']);
            return;
        }
        
        try {
            $stmt = $this->db->prepare("INSERT INTO positions (race_id, position, driver_name, vehicle_id, time_seconds, points) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['race_id'],
                $data['position'],
                $data['driver_name'],
                $data['vehicle_id'] ?? null,
                $data['time_seconds'] ?? null,
                $data['points'] ?? 0
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Posición creada correctamente',
                'position_id' => $this->db->lastInsertId()
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                http_response_code(409);
                echo json_encode(['error' => 'Ya existe esa posición en la carrera']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al crear posición: ' . $e->getMessage()]);
            }
        }
    }
    
    /**
     * Actualizar posición (solo admin)
     */
    public function update($id) {
        $this->auth->requireAdmin();
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        try {
            $stmt = $this->db->prepare("UPDATE positions SET position = ?, driver_name = ?, vehicle_id = ?, time_seconds = ?, points = ? WHERE id = ?");
            $stmt->execute([
                $data['position'] ?? null,
                $data['driver_name'] ?? null,
                $data['vehicle_id'] ?? null,
                $data['time_seconds'] ?? null,
                $data['points'] ?? 0,
                $id
            ]);
            
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Posición no encontrada']);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Posición actualizada correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar posición']);
        }
    }
    
    /**
     * Eliminar posición (solo admin)
     */
    public function delete($id) {
        $this->auth->requireAdmin();
        
        try {
            $stmt = $this->db->prepare("DELETE FROM positions WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                http_response_code(404);
                echo json_encode(['error' => 'Posición no encontrada']);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Posición eliminada correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar posición']);
        }
    }
    
    /**
     * Actualizar múltiples posiciones de una carrera (solo admin)
     */
    public function updateBulk() {
        $this->auth->requireAdmin();
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['race_id']) || empty($data['positions'])) {
            http_response_code(400);
            echo json_encode(['error' => 'race_id y positions son requeridos']);
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Eliminar posiciones existentes
            $stmt = $this->db->prepare("DELETE FROM positions WHERE race_id = ?");
            $stmt->execute([$data['race_id']]);
            
            // Insertar nuevas posiciones
            $stmt = $this->db->prepare("INSERT INTO positions (race_id, position, driver_name, vehicle_id, time_seconds, points) VALUES (?, ?, ?, ?, ?, ?)");
            
            foreach ($data['positions'] as $pos) {
                $stmt->execute([
                    $data['race_id'],
                    $pos['position'],
                    $pos['driver_name'],
                    $pos['vehicle_id'] ?? null,
                    $pos['time_seconds'] ?? null,
                    $pos['points'] ?? 0
                ]);
            }
            
            $this->db->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Posiciones actualizadas correctamente'
            ]);
        } catch (Exception $e) {
            $this->db->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar posiciones: ' . $e->getMessage()]);
        }
    }
}

