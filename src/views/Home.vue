<script setup>
import Header from "../components/HeaderComponent.vue"  
import Footer from "../components/footercomponent.vue"  
</script>

<template>
  <Header />
  <div class="banner">
    <div class="tilt">
      <div class="content">
        <h1>Podium</h1>
        <p>
          Welcome to Podium, a streamlined and intuitive race management system. Podium simplifies the organization and monitoring of competitive events by providing an efficient platform for scheduling, 
          tracking, and analyzing races. Designed with both administrators and participants in mind, it offers clear interfaces, real-time results, and comprehensive performance insights. Whether you're 
          managing a local event or a professional racing series, Podium ensures accuracy, transparency, and ease of use—so you can focus on what truly matters: the race itself.
        </p>
        <router-link to="/categories">See Results</router-link>
      </div>
    </div>
  </div>

  <Footer />
</template>

<style>
body{
    min-height: 100vh;
    background-image: url("../public/background2.png");
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
}

.banner .tilt{
    position: absolute;
    content: '';
    top: 6rem;
    left: 2rem;
    right: 0rem;
    bottom: -1rem;
    background: linear-gradient(30deg, rgba(0,0,0,0.6), transparent);
    box-shadow: inset 0 0 25px rgba(0,0,0,0.2);
    backdrop-filter: blur(10px);
    border: 1rem solid rgba(0,0,0,0.8);
    border-right: none;  
    padding: 30px 58px 0;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    border-radius: 20px;
    transition: 1s;
    transition-delay: 0.5s;
}

.banner .tilt.active{
    right: 30px;
    border-right: 15px solid rgba(0,0,0,0.8);
}

.banner .content{
    position: relative;
    max-width: 45%;
    max-height: 50%;
    display: flex;
    justify-content: center;
    align-items: start;
    flex-direction: column;
}

.banner .content h1{
    color: white;
    font-size: 5em;
    text-transform: uppercase;
    letter-spacing: 3px;
}

.banner .content p{
    color: white;
    font-size: 1em;
    font-weight: 200;
    letter-spacing: 1px;
}

.banner .content a{
    margin-top: 50px;
    text-decoration: none;
    color: white;
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.5);
    backdrop-filter: (20px);
    box-shadow: inset 0 0 25px rgba(0,0,0,0.2);
    padding: 10px 40px;
    letter-spacing: 1px;
    font-weight: 200;
    text-transform: uppercase;
    transition: 0.5s;
}

.banner .content a:hover{
    box-shadow: inset 0 0 25px rgba(255, 255, 255, 0.5);
}

Footer{
    margin: 50rem 0 0 0;
}
</style>
