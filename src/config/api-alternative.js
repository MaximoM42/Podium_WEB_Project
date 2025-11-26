// Configuración alternativa del API (sin usar .htaccess)
// Usa este archivo si no puedes habilitar mod_rewrite en Apache

export const API_URL = import.meta.env.VITE_API_URL || 'http://localhost/backend/index.php';

// Helper para construir URLs con query string
function buildUrl(endpoint) {
  // Convertir /categories a ?resource=categories
  const parts = endpoint.split('/').filter(Boolean);
  
  if (parts.length === 0) return API_URL;
  
  let url = `${API_URL}?resource=${parts[0]}`;
  
  // Añadir ID si existe
  if (parts.length > 1) {
    url += `&id=${parts[1]}`;
  }
  
  // Añadir acción si existe
  if (parts.length > 2) {
    url += `&action=${parts[2]}`;
  }
  
  return url;
}

// Helper para hacer peticiones (versión alternativa)
export async function apiRequest(endpoint, options = {}) {
  const url = buildUrl(endpoint);
  
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

// Helper para peticiones autenticadas
export async function authenticatedRequest(endpoint, firebaseUid, options = {}) {
  return apiRequest(endpoint, {
    ...options,
    headers: {
      ...options.headers,
      'Authorization': `Bearer ${firebaseUid}`
    }
  });
}

