// Configuración del API

export const API_URL = import.meta.env.VITE_API_URL || 'http://localhost/backend';

// Helper para hacer peticiones autenticadas
export async function apiRequest(endpoint, options = {}) {
  const url = `${API_URL}${endpoint}`;
  
  // Agregar headers por defecto
  const headers = {
    'Content-Type': 'application/json',
    ...options.headers
  };
  
  const config = {
    ...options,
    headers
  };
  
  try {
    const response = await fetch(url, config);
    const data = await response.json();
    
    if (!response.ok) {
      throw new Error(data.error || 'Error en la petición');
    }
    
    return data;
  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
}

// Helper para peticiones autenticadas con Firebase UID
export async function authenticatedRequest(endpoint, firebaseUid, options = {}) {
  return apiRequest(endpoint, {
    ...options,
    headers: {
      ...options.headers,
      'Authorization': `Bearer ${firebaseUid}`
    }
  });
}

