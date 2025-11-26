import { createApp } from "vue";
import "./style.css";
import App from "./App.vue";
import router from "./router";

import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
import { firebaseConfig } from "./config/firebase";

// Inicializar Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);

createApp(App).use(router).mount("#app");
/*
#000000 — negro

#c1b0cc — lila claro

#503f70 — violeta oscuro

#548aba — azul medio

#c32495 — fucsia intenso*/
