import { createRouter, createWebHistory } from "vue-router";
import Home from "../views/Home.vue";
import Categories from "../views/categories/categories.vue";
import Forum from "../views/forum/forum.vue";
import Login from "../views/login/login.vue";

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
