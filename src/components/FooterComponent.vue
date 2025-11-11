<script setup>
import { useRouter } from 'vue-router';
</script>

<template>  
    <footer class="footer">
        <div class="container">
            <div class="footer-row">
                <div class="footer-links">
                    <h4>About</h4>
                    <ul>
                        <li><router-link to="/about">Services</router-link></li>
                        <li><router-link to="/about">Privacy policies</router-link></li>
                        <li><router-link to="/about">Copyright</router-link></li>
                    </ul>
                </div>

                <div class="footer-links">
                    <h4>Contact</h4>
                    <div class="social-link">
                        <a href="mailto:" class="icon"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#FFFFFF"><path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm320-280L160-640v400h640v-400L480-440Zm0-80 320-200H160l320 200ZM160-640v-80 480-400Z"/></svg></a>
                        <a href="https://x.com/MDSwSol" class="icon"><svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="#FFFFFF" class="ionicon" viewBox="0 0 512 512"><path d="M496 109.5a201.8 201.8 0 01-56.55 15.3 97.51 97.51 0 0043.33-53.6 197.74 197.74 0 01-62.56 23.5A99.14 99.14 0 00348.31 64c-54.42 0-98.46 43.4-98.46 96.9a93.21 93.21 0 002.54 22.1 280.7 280.7 0 01-203-101.3A95.69 95.69 0 0036 130.4c0 33.6 17.53 63.3 44 80.7A97.5 97.5 0 0135.22 199v1.2c0 47 34 86.1 79 95a100.76 100.76 0 01-25.94 3.4 94.38 94.38 0 01-18.51-1.8c12.51 38.5 48.92 66.5 92.05 67.3A199.59 199.59 0 0139.5 405.6a203 203 0 01-23.5-1.4A278.68 278.68 0 00166.74 448c181.36 0 280.44-147.7 280.44-275.8 0-4.2-.11-8.4-.31-12.5A198.48 198.48 0 00496 109.5z"/></svg></a>
                        <a href="https://www.instagram.com/mdsoftwaresolutions/" class="icon"><svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="#FFFFFF" class="ionicon" viewBox="0 0 512 512"><path d="M349.33 69.33a93.62 93.62 0 0193.34 93.34v186.66a93.62 93.62 0 01-93.34 93.34H162.67a93.62 93.62 0 01-93.34-93.34V162.67a93.62 93.62 0 0193.34-93.34h186.66m0-37.33H162.67C90.8 32 32 90.8 32 162.67v186.66C32 421.2 90.8 480 162.67 480h186.66C421.2 480 480 421.2 480 349.33V162.67C480 90.8 421.2 32 349.33 32z"/><path d="M377.33 162.67a28 28 0 1128-28 27.94 27.94 0 01-28 28zM256 181.33A74.67 74.67 0 11181.33 256 74.75 74.75 0 01256 181.33m0-37.33a112 112 0 10112 112 112 112 0 00-112-112z"/></svg></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<style>
*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    list-style: none;
}
    
body{
    top: 10rem;
    font-family: 'Times New Roman', Times, serif;
    text-transform: capitalize;
}

.container{
    max-width: 100%;
    margin: 0 auto;
}

.footer {
    background-color: #1E1E1E;
    padding: 3rem 0 0 0;
}

.footer-row{
    display: flex;
    flex-wrap: wrap;
}

.footer-links{
    width: 25%;
    padding: 0 3rem;
}

.footer-links h4{
    font-size: 2rem;
    color: white;
    margin-top: 1rem;
    margin-bottom: 2rem;
    font-weight: 500;
    border-bottom: 0.2rem solid #ff00dd;
    padding-bottom: 0.5rem;
    display: inline-block;
}

.footer-links ul li a{
    font-size: 1.5rem;
    text-decoration: none;
    color: white;
    display: block;
    margin-bottom: 1rem;
    transition: all .3s ease;
}

.footer-links ul li a:hover{
    color: white;
    padding-left: 0.5rem;
}

.icon{
    color: white;
    display: inline-block;
    margin: 0 0.5rem;
    transition: all .3s ease;
}

@media(max-width:50rem){
    .footer-row {
        text-align: center;
    }

    .footer-links {
        width: 100%;
        margin-bottom: 1.5rem;
    }
}
</style>