/**
 * PODIUM - Configuración de API
 * Cliente Axios configurado para comunicarse con el backend PHP
 */

import axios from 'axios';

// URL base del backend (ajustar según tu configuración)
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost/Podium_WEB_Project/backend';

// Crear instancia de Axios
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  withCredentials: true, // Importante para sesiones PHP
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  timeout: 15000 // 15 segundos
});

// Interceptor para requests (agregar tokens si es necesario)
apiClient.interceptors.request.use(
  config => {
    // Aquí podrías agregar tokens de autenticación si usas JWT
    // const token = localStorage.getItem('token');
    // if (token) {
    //   config.headers.Authorization = `Bearer ${token}`;
    // }
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

// Interceptor para responses (manejo global de errores)
apiClient.interceptors.response.use(
  response => {
    // Si la respuesta tiene success: false, tratar como error
    if (response.data && response.data.success === false) {
      return Promise.reject({
        response: {
          data: response.data,
          status: response.status
        }
      });
    }
    return response;
  },
  error => {
    // Manejo de errores
    if (error.response) {
      // Error con respuesta del servidor
      const { status, data } = error.response;
      
      console.error(`API Error ${status}:`, data);
      
      // Casos específicos
      switch (status) {
        case 401:
          // No autenticado - redirigir a login si es necesario
          console.warn('No autenticado - Sesión expirada');
          break;
        case 403:
          console.warn('Acceso denegado - Sin permisos');
          break;
        case 404:
          console.warn('Recurso no encontrado');
          break;
        case 500:
          console.error('Error del servidor');
          break;
      }
    } else if (error.request) {
      // Request hecho pero sin respuesta
      console.error('No se recibió respuesta del servidor:', error.request);
    } else {
      // Error al configurar el request
      console.error('Error al configurar request:', error.message);
    }
    
    return Promise.reject(error);
  }
);

export default apiClient;
