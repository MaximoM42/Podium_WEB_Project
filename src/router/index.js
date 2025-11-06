import { createRouter, createWebHistory } from "vue-router";
import Home from "../views/Home.vue";
import Categories from "../views/categories.vue";
import Forum from "../views/forum.vue";
import Login from "../views/login.vue";
import About from "../views/about.vue";
import Register from "../views/register.vue";

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
			path: "/forum",
			name: "Forum",
			component: Forum,
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
	]
});

export default router;
