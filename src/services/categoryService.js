// Servicio para gestión de categorías

import { apiRequest, authenticatedRequest } from '../config/api';

export const categoryService = {
  // Obtener todas las categorías
  async getAll() {
    return apiRequest('/categories', { method: 'GET' });
  },
  
  // Obtener categoría por ID
  async getById(id) {
    return apiRequest(`/categories/${id}`, { method: 'GET' });
  },
  
  // Crear categoría (solo admin)
  async create(category, firebaseUid) {
    return authenticatedRequest('/categories', firebaseUid, {
      method: 'POST',
      body: JSON.stringify(category)
    });
  },
  
  // Actualizar categoría (solo admin)
  async update(id, category, firebaseUid) {
    return authenticatedRequest(`/categories/${id}`, firebaseUid, {
      method: 'PUT',
      body: JSON.stringify(category)
    });
  },
  
  // Eliminar categoría (solo admin)
  async delete(id, firebaseUid) {
    return authenticatedRequest(`/categories/${id}`, firebaseUid, {
      method: 'DELETE'
    });
  }
};

