/**
 * PODIUM - Servicio de Categorías
 * Maneja todas las peticiones relacionadas con categorías
 */

import apiClient from './api';

export default {
  /**
   * Obtener todas las categorías
   * @returns {Promise}
   */
  async listar() {
    try {
      const response = await apiClient.get('/api/categorias/listar.php');
      return response.data;
    } catch (error) {
      throw error.response?.data || { 
        success: false, 
        message: 'Error al obtener categorías' 
      };
    }
  }
};
