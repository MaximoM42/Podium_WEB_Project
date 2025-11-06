import { createApp } from "vue";
import "./style.css";
import App from "./App.vue";
import router from "./router";


import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";

const firebaseConfig = {
  apiKey: "AIzaSyCIOZRPQrE4lYptmOttZxjoq32kRkpvbpo",
  authDomain: "podium-19f2e.firebaseapp.com",
  projectId: "podium-19f2e",
  storageBucket: "podium-19f2e.firebasestorage.app",
  messagingSenderId: "899048605481",
  appId: "1:899048605481:web:3c33246fd2f953dfced938",
  measurementId: "G-SSQRYLYC6D"
};

const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);


createApp(App).use(router).mount("#app");
/*
#000000 — negro

#c1b0cc — lila claro

#503f70 — violeta oscuro

#548aba — azul medio

#c32495 — fucsia intenso*/
