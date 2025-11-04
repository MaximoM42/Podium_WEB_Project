<script setup>
import "../styles/styleNav.css"

const showSidebar = () => {
    const sidebar = document.querySelector('.sidebar');
    sidebar.style.display = 'flex';
}

const hideSidebar = () => {
    const sidebar = document.querySelector('.sidebar');
    sidebar.style.display = 'none';
}

</script>

<template>
    <div>
        <nav class="header">
            <ul class="sidebar">
                <li @click="hideSidebar()"><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></a></li>
                <li><a href="../App.vue" class="logo">Podium</a></li>
                <li><a href="#" class="">Home</a></li>
                <li><a href="#" class="">About</a></li>
                <li><a href="#">Categories</a></li>
                <li><a href="#">Forum</a></li>
                <li><a href="#">Login</a></li>
            </ul>

            <ul>
                <li><a href="../App.vue" class="logo">Podium</a></li>
                <li class="hideOnMobile"><a href="#" class="">Home</a></li>
                <li class="hideOnMobile"><a href="#" class="">About Us</a></li>
                <li class="hideOnMobile"><a href="#">Categories</a></li>
                <li class="hideOnMobile"><a href="#">Forum</a></li>
                <li class="hideOnMobile"><a href="#">Login</a></li>
                <li class="menu-button" @click="showSidebar()"><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg></a></li>
            </ul>
        </nav>
    </div>
</template>

