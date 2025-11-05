<template>
    <Header />
    <div class="categories-container">
      <h1>Categories</h1>
      <p>Página de categorías</p>
    </div>
    
    <Footer />
  </template>
  
  <script setup>
  import Header from "../components/HeaderComponent.vue"  
  import Footer from "../components/FooterComponent.vue" 
  </script>
  
  <style scoped>
  .categories-container {
    padding: 2rem;
  }
  </style>
  