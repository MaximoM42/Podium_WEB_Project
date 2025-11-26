// Servicio para gestión de vehículos

import { apiRequest, authenticatedRequest } from '../config/api';

export const vehicleService = {
  // Obtener todos los vehículos
  async getAll(categoryId = null) {
    const query = categoryId ? `?category_id=${categoryId}` : '';
    return apiRequest(`/vehicles${query}`, { method: 'GET' });
  },
  
  // Obtener vehículo por ID
  async getById(id) {
    return apiRequest(`/vehicles/${id}`, { method: 'GET' });
  },
  
  // Crear vehículo (solo admin)
  async create(vehicle, firebaseUid) {
    return authenticatedRequest('/vehicles', firebaseUid, {
      method: 'POST',
      body: JSON.stringify(vehicle)
    });
  },
  
  // Actualizar vehículo (solo admin)
  async update(id, vehicle, firebaseUid) {
    return authenticatedRequest(`/vehicles/${id}`, firebaseUid, {
      method: 'PUT',
      body: JSON.stringify(vehicle)
    });
  },
  
  // Eliminar vehículo (solo admin)
  async delete(id, firebaseUid) {
    return authenticatedRequest(`/vehicles/${id}`, firebaseUid, {
      method: 'DELETE'
    });
  }
};

