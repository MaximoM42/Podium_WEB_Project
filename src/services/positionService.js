// Servicio para gestión de posiciones

import { authenticatedRequest, apiRequest } from '../config/api';

export const positionService = {
  // Obtener posiciones de una carrera
  async getByRace(raceId) {
    return apiRequest(`/positions/${raceId}`, { method: 'GET' });
  },
  
  // Crear posición (solo admin)
  async create(position, firebaseUid) {
    return authenticatedRequest('/positions', firebaseUid, {
      method: 'POST',
      body: JSON.stringify(position)
    });
  },
  
  // Actualizar posición (solo admin)
  async update(id, position, firebaseUid) {
    return authenticatedRequest(`/positions/${id}`, firebaseUid, {
      method: 'PUT',
      body: JSON.stringify(position)
    });
  },
  
  // Eliminar posición (solo admin)
  async delete(id, firebaseUid) {
    return authenticatedRequest(`/positions/${id}`, firebaseUid, {
      method: 'DELETE'
    });
  },
  
  // Actualizar múltiples posiciones (solo admin)
  async updateBulk(raceId, positions, firebaseUid) {
    return authenticatedRequest('/positions/bulk', firebaseUid, {
      method: 'POST',
      body: JSON.stringify({ race_id: raceId, positions })
    });
  }
};

