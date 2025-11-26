// Servicio para gestión de carreras

import { apiRequest, authenticatedRequest } from '../config/api';

export const raceService = {
  // Obtener todas las carreras
  async getAll(categoryId = null) {
    const query = categoryId ? `?category_id=${categoryId}` : '';
    return apiRequest(`/races${query}`, { method: 'GET' });
  },
  
  // Obtener carrera por ID con posiciones
  async getById(id) {
    return apiRequest(`/races/${id}`, { method: 'GET' });
  },
  
  // Obtener carreras de una categoría con posiciones
  async getByCategoryWithPositions(categoryId) {
    return apiRequest(`/races/${categoryId}/positions`, { method: 'GET' });
  },
  
  // Crear carrera (solo admin)
  async create(race, firebaseUid) {
    return authenticatedRequest('/races', firebaseUid, {
      method: 'POST',
      body: JSON.stringify(race)
    });
  },
  
  // Actualizar carrera (solo admin)
  async update(id, race, firebaseUid) {
    return authenticatedRequest(`/races/${id}`, firebaseUid, {
      method: 'PUT',
      body: JSON.stringify(race)
    });
  },
  
  // Eliminar carrera (solo admin)
  async delete(id, firebaseUid) {
    return authenticatedRequest(`/races/${id}`, firebaseUid, {
      method: 'DELETE'
    });
  }
};

