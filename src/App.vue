<script setup>
import Header from "./components/Headercomponent.vue"
import Footer from "./components/FooterComponent.vue"
import "./styles/styleHome.css"
</script>

<template>
  <title>Podium</title>
  <Header></Header>
  
  <div class="banner">
    <div class="tilt">
      <div class="content">
        <h1>Podium</h1>
        <p>
          Welcome to Podium, a streamlined and intuitive race management system. Podium simplifies the organization and monitoring of competitive events by providing an efficient platform for scheduling, 
          tracking, and analyzing races. Designed with both administrators and participants in mind, it offers clear interfaces, real-time results, and comprehensive performance insights. Whether you’re 
          managing a local event or a professional racing series, Podium ensures accuracy, transparency, and ease of use—so you can focus on what truly matters: the race itself.
        </p>
        <a href="#">See results</a>
      </div>
    </div>
  </div>

  <Footer class="fot"></Footer>
</template>

<style></style>