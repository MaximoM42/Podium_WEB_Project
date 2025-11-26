import { createRouter, createWebHistory } from "vue-router";
import { getAuth, onAuthStateChanged } from "firebase/auth";
import Home from "../views/Home.vue";
import Categories from "../views/categories.vue";
import Login from "../views/login.vue";
import About from "../views/about.vue";
import Register from "../views/register.vue";
import Races from "../views/races.vue";

// Admin views
import Dashboard from "../views/admin/Dashboard.vue";
import CategoriesManagement from "../views/admin/CategoriesManagement.vue";
import VehiclesManagement from "../views/admin/VehiclesManagement.vue";
import RacesManagement from "../views/admin/RacesManagement.vue";

const router = createRouter({
	history: createWebHistory(),
	routes: [
		{
			path: "/",
			name: "Home",
			component: Home,
		},
		{
			path: "/categories",
			name: "Categories",
			component: Categories,
		},
		{
			path: "/login",
			name: "Login",
			component: Login,
		},
		{
			path: "/About",
			name: "About",
			component: About,
		},
		{
			path: "/Register",
			name: "Register",
			component: Register,
		},
		{
			path: "/categories/:id",
			name: "Races",
			component: Races,
		},
		// Admin Routes
		{
			path: "/admin",
			name: "AdminDashboard",
			component: Dashboard,
			meta: { requiresAuth: true, requiresAdmin: true }
		},
		{
			path: "/admin/categories",
			name: "AdminCategories",
			component: CategoriesManagement,
			meta: { requiresAuth: true, requiresAdmin: true }
		},
		{
			path: "/admin/vehicles",
			name: "AdminVehicles",
			component: VehiclesManagement,
			meta: { requiresAuth: true, requiresAdmin: true }
		},
		{
			path: "/admin/races",
			name: "AdminRaces",
			component: RacesManagement,
			meta: { requiresAuth: true, requiresAdmin: true }
		},
	]
});

// Guard de navegación para proteger rutas
router.beforeEach(async (to, from, next) => {
	const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
	const requiresAdmin = to.matched.some(record => record.meta.requiresAdmin);
	
	if (requiresAuth || requiresAdmin) {
		const auth = getAuth();
		
		// Esperar a que Firebase verifique el estado de autenticación
		const user = await new Promise((resolve) => {
			const unsubscribe = onAuthStateChanged(auth, (user) => {
				unsubscribe();
				resolve(user);
			});
		});
		
		if (!user) {
			// No autenticado, redirigir a login
			next('/login');
		} else if (requiresAdmin) {
			// Verificar rol de admin (se hace en el componente también)
			// Aquí solo verificamos que esté autenticado
			next();
		} else {
			next();
		}
	} else {
		next();
	}
});

export default router;
