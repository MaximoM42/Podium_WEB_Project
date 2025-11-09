<script setup>
/* 
    Logica de carga de categorias, en un principio se tendria que hardcodear y despues estar automatizado,
    las categorias se tendrian que levantar de la bd y despues ser cargadas.
*/
const categories = [
    {
        title: "TC",
        name: "Turismo Carretera",
        src: "/tc.jpg",
        rtr: "#", //router
    },
    {
        title: "TCP",
        name: "Turismo Carretera Pista",
        src: "/tcp.jpg",
        rtr: "#", //router
    },
    {
        title: "TCPK",
        name: "Turismo Carretera Pick Ups",
        src: "/tcpk.jpg",
        rtr: "#", //router
    },
]
</script>

<template>
    <div class="card-container">
        <div class="card" v-for="category in categories" :key="category.title">
            <img :src="category.src" :alt="category.name">
            <div class="card-content">
                <h2>{{ category.title }}</h2>
                <p>{{ category.name }}</p>
                <router-link :to="category.rtr" class="btn">See Races</router-link>
            </div>
        </div>
    </div>
</template>

<style scoped>
.card-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px; /* Espacio entre las tarjetas */
    justify-content: center;
    padding: 20px;
}

.card {
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    width: 300px;
    transition: transform 0.2s;
    backdrop-filter: blur(10px);
}

.card:hover {
    transform: translateY(-5px);
}

.card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.card-content {
    padding: 16px;
}

.card-content h2 {
    color: white;
    margin-top: 0;
    margin-bottom: 8px;
}

.card-content p {
    color: #cdcdcd;
    margin-bottom: 16px;
}

.btn {
    display: inline-block;
    margin-top: 16px;
    text-decoration: none;
    color: #ffffff;
    background: transparent;
    border: 1px solid rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    box-shadow: inset 0 0 15px rgba(0,0,0,0.1);
    padding: 10px 30px;
    letter-spacing: 1px;
    font-weight: 400;
    text-transform: uppercase;
    transition: 0.5s;
    border-radius: 5px;
    text-align: center;
}

.btn:hover {
    background: transparent;
    box-shadow: inset 0 0 25px rgba(255, 255, 255, 0.5);
}

</style>

