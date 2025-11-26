// Servicio para gestión de usuarios

import { apiRequest, authenticatedRequest } from '../config/api';

export const userService = {
  // Crear o actualizar usuario después del registro/login
  async createOrUpdate(firebaseUid, email, role = 'user') {
    return apiRequest('/users', {
      method: 'POST',
      body: JSON.stringify({ firebase_uid: firebaseUid, email, role })
    });
  },
  
  // Obtener usuario actual
  async getCurrentUser(firebaseUid) {
    return authenticatedRequest('/users/current', firebaseUid, {
      method: 'GET'
    });
  },
  
  // Listar todos los usuarios (solo admin)
  async listAll(firebaseUid) {
    return authenticatedRequest('/users', firebaseUid, {
      method: 'GET'
    });
  },
  
  // Actualizar rol de usuario (solo admin)
  async updateRole(userId, role, firebaseUid) {
    return authenticatedRequest(`/users/${userId}/role`, firebaseUid, {
      method: 'PUT',
      body: JSON.stringify({ role })
    });
  }
};

