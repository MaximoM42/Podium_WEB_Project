// Composable para gestión de autenticación y roles

import { ref, onMounted } from 'vue';
import { getAuth, onAuthStateChanged } from 'firebase/auth';
import { userService } from '../services/userService';

const isLoggedIn = ref(false);
const currentUser = ref(null);
const userRole = ref(null);
const loading = ref(true);

export const useAuth = () => {
    const auth = getAuth();
    
    const loadUserData = async (firebaseUser) => {
        if (!firebaseUser) {
            isLoggedIn.value = false;
            currentUser.value = null;
            userRole.value = null;
            return;
        }
        
        try {
            // Intentar obtener datos del usuario desde MySQL
            const response = await userService.getCurrentUser(firebaseUser.uid);
            
            currentUser.value = {
                ...firebaseUser,
                role: response.user.role,
                dbId: response.user.id
            };
            userRole.value = response.user.role;
            isLoggedIn.value = true;
        } catch (error) {
            console.error('Error al cargar datos del usuario:', error);
            
            // Si no existe en MySQL, crearlo
            try {
                await userService.createOrUpdate(firebaseUser.uid, firebaseUser.email);
                
                // Recargar datos
                const response = await userService.getCurrentUser(firebaseUser.uid);
                currentUser.value = {
                    ...firebaseUser,
                    role: response.user.role,
                    dbId: response.user.id
                };
                userRole.value = response.user.role;
                isLoggedIn.value = true;
            } catch (createError) {
                console.error('Error al crear usuario:', createError);
                // Asignar rol de usuario por defecto
                currentUser.value = firebaseUser;
                userRole.value = 'user';
                isLoggedIn.value = true;
            }
        }
    };
    
    onMounted(() => {
        onAuthStateChanged(auth, async (user) => {
            loading.value = true;
            await loadUserData(user);
            loading.value = false;
        });
    });
    
    const isAdmin = () => {
        return userRole.value === 'admin';
    };
    
    const getFirebaseUid = () => {
        return currentUser.value?.uid || null;
    };
    
    return {
        isLoggedIn,
        currentUser,
        userRole,
        loading,
        isAdmin,
        getFirebaseUid,
        auth
    };
};

