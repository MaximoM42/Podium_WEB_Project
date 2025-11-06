<script setup>
import { onMounted } from 'vue'
import "../styles/styleNav.css"

const showSidebar = () => {
  const sidebar = document.querySelector('.sidebar')
  if (sidebar) sidebar.style.display = 'flex'
}

const hideSidebar = () => {
  const sidebar = document.querySelector('.sidebar')
  if (sidebar) sidebar.style.display = 'none'
}

onMounted(() => {
  const navLinks = document.querySelectorAll('nav a')

  navLinks.forEach(link => {
    link.addEventListener('mousemove', e => {
      const rect = link.getBoundingClientRect()
      const x = e.clientX - rect.left
      const y = e.clientY - rect.top
      link.style.setProperty('--x', `${x}px`)
      link.style.setProperty('--y', `${y}px`)
    })
  })
})
</script>

<template>
  <div>
    <nav class="header">
      <ul class="sidebar">
        <li @click="hideSidebar()">
          <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                 width="24px" fill="#e3e3e3">
              <path
                d="m256-200-56-56 224-224-224-224 56-56 224 224
                   224-224 56 56-224 224 224 224-56 56-224-224-224 224Z" />
            </svg>
          </a>
        </li>
        <li><router-link to="/" class="logo">Podium</router-link></li>
        <li><router-link to="/">Home</router-link></li>
        <li><router-link to="/carreras">Carreras</router-link></li>
        <li><router-link to="/categories">Categories</router-link></li>
        <li><router-link to="/forum">Forum</router-link></li>
        <li><router-link to="/login">Login</router-link></li>
      </ul>

      <ul>
        <li><router-link to="/" class="logo">Podium</router-link></li>
        <li class="hideOnMobile"><router-link to="/">Home</router-link></li>
        <li class="hideOnMobile"><router-link to="/carreras">Carreras</router-link></li>
        <li class="hideOnMobile"><router-link to="/categories">Categories</router-link></li>
        <li class="hideOnMobile"><router-link to="/forum">Forum</router-link></li>
        <li class="hideOnMobile"><router-link to="/login">Login</router-link></li>
        <li class="menu-button" @click="showSidebar()">
          <a href="#">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                 width="24px" fill="#e3e3e3">
              <path
                d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Z
                   m0-200v-80h720v80H120Z" />
            </svg>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</template>
