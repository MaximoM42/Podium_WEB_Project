<script setup>
import "../styles/styleNav.css"
import "./HeaderMovement"
</script>

<template>
    <div>
        <nav class="header">
            <ul class="sidebar">
                <li onclick="hideSidebar()"><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></a></li>
                <li><a href="../index.html" class="logo">Podium</a></li>
                <li><a href="../index.html" class="">Home</a></li>
                <li><a href="../Results/index.html">Results</a></li>
                <li><a href="../Forum/index.html">Forum</a></li>
                <li><a href="../Login/index.html">Login</a></li>
            </ul>

            <ul>
                <li><a href="../index.html" class="logo">Podium</a></li>
                <li class="hideOnMobile"><a href="../index.html" class="">Home</a></li>
                <li class="hideOnMobile"><a href="../Results/index.html">Results</a></li>
                <li class="hideOnMobile"><a href="../Forum/index.html">Forum</a></li>
                <li class="hideOnMobile"><a href="../Login/index.html">Login</a></li>
                <li class="menu-button" onclick="showSidebar()"><a href="#"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg></a></li>
            </ul>
        </nav>
    </div>
</template>

