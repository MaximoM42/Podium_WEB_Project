// Configuración de Firebase usando variables de entorno

export const firebaseConfig = {
  apiKey: import.meta.env.VITE_FIREBASE_API_KEY || "AIzaSyCIOZRPQrE4lYptmOttZxjoq32kRkpvbpo",
  authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN || "podium-19f2e.firebaseapp.com",
  projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID || "podium-19f2e",
  storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET || "podium-19f2e.firebasestorage.app",
  messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID || "899048605481",
  appId: import.meta.env.VITE_FIREBASE_APP_ID || "1:899048605481:web:3c33246fd2f953dfced938",
  measurementId: import.meta.env.VITE_FIREBASE_MEASUREMENT_ID || "G-SSQRYLYC6D"
};

