<script setup>

const showSidebar = () => {
  const sidebar = document.querySelector('.sidebar')
  if (sidebar) sidebar.style.display = 'flex'
}

const hideSidebar = () => {
  const sidebar = document.querySelector('.sidebar')
  if (sidebar) sidebar.style.display = 'none'
}
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
        <li><router-link to="/about">About</router-link></li>
        <li><router-link to="/categories">Categories</router-link></li>
        <li><router-link to="/forum">Forum</router-link></li>
        <li><router-link to="/login">Login</router-link></li>
      </ul>

      <ul>
        <li><router-link to="/" class="logo">Podium</router-link></li>
        <li class="hideOnMobile"><router-link to="/">Home</router-link></li>
        <li class="hideOnMobile"><router-link to="/about">About Us</router-link></li>
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

<style>
* {
    margin: 0;
    padding: 0;
}

body {
    min-height: 100vh;
    font-family: 'Times New Roman', Times, serif;
}

nav {
    background-color: #1E1E1E;
    box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.1);
}

nav ul {
    width: 100%;
    list-style: none;
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

nav li {
    height: 50px;
}

nav a {
    height: 100%;
    padding: 0 30px;
    text-decoration: none;
    display: flex;
    align-items: center;
    color: white;
}

nav a:hover {
    background-color: rgb(62, 62, 62);
}

nav li:first-child {
    margin-right: auto;
}

.sidebar {
    position: fixed;
    top: 0;
    right: 0;
    height: 100vh;
    width: 250px;
    z-index: 999;
    background-color: rgb(0, 0, 0, 0.2);
    backdrop-filter: blur(10px);
    box-shadow: -10px 0 10px rgba(0, 0, 0, 0.1);
    display: none;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
}

.sidebar li {
    width: 100%;
}

.sidebar a {
    width: 100%;
}

.menu-button {
    display: none;
}

@media(max-width: 70rem) {
    .hideOnMobile {
        display: none;
    }

    .menu-button {
        display: block;
    }
}

@media(max-width: 500px) {
    .sidebar {
        width: 100%;
    }
}
</style>
