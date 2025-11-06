import { createRouter, createWebHistory } from "vue-router";
import Home from "../views/Home.vue";
import Categories from "../views/categories/categories.vue";
import Forum from "../views/forum/forum.vue";
import Login from "../views/login/login.vue";

// Componentes de Carreras
import CarrerasList from "../components/CarrerasList.vue";
import CarreraDetalle from "../components/CarreraDetalle.vue";
import InscripcionForm from "../components/InscripcionForm.vue";

const routes = [
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
		path: "/carreras",
		name: "Carreras",
		component: CarrerasList,
	},
	{
		path: "/carreras/:id",
		name: "CarreraDetalle",
		component: CarreraDetalle,
	},
	{
		path: "/inscripcion/:id",
		name: "InscripcionForm",
		component: InscripcionForm,
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
];

const router = createRouter({
	history: createWebHistory(),
	routes,
});

export default router;
