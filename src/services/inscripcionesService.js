/**
 * PODIUM - Servicio de Inscripciones
 * Maneja todas las peticiones relacionadas con inscripciones
 */

import apiClient from './api';

export default {
  /**
   * Crear nueva inscripción a una carrera
   * @param {Object} datos - Datos de la inscripción
   * @returns {Promise}
   */
  async crear(datos) {
    try {
      const response = await apiClient.post('/Terminal_carga_productos.php', datos);
      return response.data;
    } catch (error) {
      throw error.response?.data || { 
        success: false, 
        message: 'Error al crear inscripción' 
      };
    }
  },

  /**
   * Obtener inscripciones del piloto autenticado
   * @param {String} firebaseUid - UID de Firebase del piloto
   * @returns {Promise}
   */
  async misInscripciones(firebaseUid) {
    try {
      const response = await apiClient.get(
        `/api/inscripciones/mis-inscripciones.php?firebase_uid=${firebaseUid}`
      );
      return response.data;
    } catch (error) {
      throw error.response?.data || { 
        success: false, 
        message: 'Error al obtener inscripciones' 
      };
    }
  }
};
