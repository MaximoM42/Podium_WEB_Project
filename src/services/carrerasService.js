/**
 * PODIUM - Servicio de Carreras
 * Maneja todas las peticiones relacionadas con carreras
 */

import apiClient from './api';

export default {
  /**
   * Obtener lista de carreras
   * @param {Object} filtros - { estado, categoria, fecha_desde, fecha_hasta }
   * @returns {Promise}
   */
  async listar(filtros = {}) {
    try {
      const params = new URLSearchParams();
      
      if (filtros.estado) params.append('estado', filtros.estado);
      if (filtros.categoria) params.append('categoria', filtros.categoria);
      if (filtros.fecha_desde) params.append('fecha_desde', filtros.fecha_desde);
      if (filtros.fecha_hasta) params.append('fecha_hasta', filtros.fecha_hasta);
      
      const queryString = params.toString();
      const url = `/api/carreras/listar.php${queryString ? '?' + queryString : ''}`;
      
      const response = await apiClient.get(url);
      return response.data;
    } catch (error) {
      throw error.response?.data || { 
        success: false, 
        message: 'Error al obtener carreras' 
      };
    }
  },

  /**
   * Obtener detalle completo de una carrera
   * @param {Number} id - ID de la carrera
   * @returns {Promise}
   */
  async obtenerDetalle(id) {
    try {
      const response = await apiClient.get(`/api/carreras/detalle.php?id=${id}`);
      return response.data;
    } catch (error) {
      throw error.response?.data || { 
        success: false, 
        message: 'Error al obtener detalle de carrera' 
      };
    }
  },

  /**
   * Obtener carreras con inscripciones abiertas
   * @returns {Promise}
   */
  async obtenerAbiertas() {
    return this.listar({ estado: 'inscripciones_abiertas' });
  },

  /**
   * Obtener carreras por categoría
   * @param {Number} idCategoria - ID de la categoría
   * @returns {Promise}
   */
  async obtenerPorCategoria(idCategoria) {
    return this.listar({ categoria: idCategoria });
  }
};
