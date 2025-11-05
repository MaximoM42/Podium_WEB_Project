<script setup>
import Header from "../components/HeaderComponent.vue"  
import Footer from "../components/footercomponent.vue" 
</script>

<template>
  <Header />
  <div class="forum-container">
    <h1>Forum</h1>
    <p>Página del foro</p>
  </div>
  <Footer />
</template>

<style scoped>
.forum-container {
  padding: 2rem;
}
</style>
