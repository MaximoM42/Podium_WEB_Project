<script setup>
import Header from "../components/HeaderComponent.vue"
import Footer from "../components/FooterComponent.vue" 
</script>

<template>
  <div class="content-format">
    <Header />

    <div class="categories-container">
      <h1>About</h1>
      <p>
        This web platform is designed to provide an efficient and modern way to manage and display race results. It allows
        users to register, view, and update information about drivers, vehicles, and race events quickly and securely.
        The goal is to deliver a reliable tool for race organizers, teams, and fans, enabling streamlined data management
        and an improved experience when analyzing or following race outcomes.
      </p>

      <h1>Services</h1>
      <p>
        The platform provides a complete environment for race data management including the registration and update of
        results driver and vehicle administration and visualization of performance statistics It ensures real time
        synchronization and secure data storage through Firebase and MySQL integration while maintaining a responsive
        interface that adapts to any device improving accessibility and user experience.
      </p>

      <h1>Privacy Policies</h1>
      <p>
        User privacy and data protection are fundamental to this project Personal data such as driver or team names are used
        exclusively for managing race information and are never shared or sold to third parties All stored data is protected
        using secure authentication and encryption mechanisms through Firebase Users may request updates or removal of their
        information by contacting the system administrator ensuring transparency and control over their personal data.
      </p>


      <h1>Copyright</h1>
      <p>
        © 2025 MD Software Solutions. All rights reserved The design source code and content of this platform are protected
        under intellectual property laws, Any form of reproduction modification or distribution without authorization from
        the developer or project team is strictly prohibited.
      </p>

    </div>

    <Footer />
  </div>
</template>
  
  
  
<style scoped>
* {
  padding: 0;
  margin: 0;
  color: white;
}

h1 {
  font-size: 3rem;
  margin: 0.63rem 0.5rem 0 0.5rem;
}

p {
  margin: 0 1.5rem 0.5rem 1.5rem;
}
.content-format {
  backdrop-filter: blur(10px);
  display: grid;
  min-height: 100dvh;
  grid-template-rows: auto 1fr auto;
}</style>
  