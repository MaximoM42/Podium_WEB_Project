<script setup>
import Header from "../components/HeaderComponent.vue"

import { ref } from "vue";
import { getAuth, createUserWithEmailAndPassword } from "firebase/auth";
import { useRouter } from 'vue-router';
import { userService } from "../services/userService";

const email = ref("");
const password = ref("");
const errMsg = ref("");
const router = useRouter();

const register = async () => {
    try {
        const data = await createUserWithEmailAndPassword(getAuth(), email.value, password.value);
        console.log("Successfully registered!");
        
        // Registrar usuario en MySQL
        try {
            await userService.createOrUpdate(data.user.uid, data.user.email, 'user');
        } catch (dbError) {
            console.error("Error al registrar en base de datos:", dbError);
        }
        
        router.push("/");
    } catch (error) {
        console.log(error.code);
        
        switch(error.code) {
            case "auth/email-already-in-use":
                errMsg.value = "This email is already registered.";
                break;
            case "auth/invalid-email":
                errMsg.value = "Invalid email format.";
                break;
            case "auth/weak-password":
                errMsg.value = "Password should be at least 6 characters.";
                break;
            default:
                errMsg.value = error.message;
                break;
        }
    }
};
</script>

<template>
  <Header />
  <div class="bodyL">
    <div class="wrapper">
      <form action="">
        <h1>Create your Account</h1>
        
        <div class="input-box">
          <input type="text" placeholder="Email" v-model="email" required>
          <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 -960 960 960" width="18px" fill="#e3e3e3"><path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z"/></svg>  
        </div>
        
        <div class="input-box">
          <input type="password" placeholder="Password" v-model="password" required>
          <svg xmlns="http://www.w3.org/2000/svg" height="15px" viewBox="0 -960 960 960" width="15px" fill="#e3e3e3"><path d="M240-80q-33 0-56.5-23.5T160-160v-400q0-33 23.5-56.5T240-640h40v-80q0-83 58.5-141.5T480-920q83 0 141.5 58.5T680-720v80h40q33 0 56.5 23.5T800-560v400q0 33-23.5 56.5T720-80H240Zm0-80h480v-400H240v400Zm240-120q33 0 56.5-23.5T560-360q0-33-23.5-56.5T480-440q-33 0-56.5 23.5T400-360q0 33 23.5 56.5T480-280ZM360-640h240v-80q0-50-35-85t-85-35q-50 0-85 35t-35 85v80ZM240-160v-400 400Z"/></svg>
        </div>
        
        <div class="error-message">
          <p v-if="errMsg">{{ errMsg }}</p><br>
        </div>
        
        <button @click.prevent="register" class="log-btn">Register</button>
        
        <div class="register-link">
          <p>
            Already have an Account? 
            <router-link to="/Login" class="register-router">Login</router-link>
          </p>
      </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.bodyL{
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
}

.wrapper{
    width: 420px;
    background-color: transparent;
    border: 2px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(20px);
    color: white;
    border-radius: 10px;
    padding: 30px 40px;
}

.wrapper h1{
    font-size: 36px;
    text-align: center;
}

.wrapper .input-box{
    width: 100%;
    height: 50px;
    margin: 30px 0;
}

.input-box input{
    width: 100%;
    height: 100%;
    background: transparent;
    border: none;
    outline: none;
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 40px;
    font-size: 16px;
    color: #ffffff;
    padding: 20px 45px 20px 20px;
}

.input-box input::placeholder{
    column-rule: white;

}

.input-box svg{
    position: absolute;
    right: 60px;
    transform: translateY(+100%);
}

.wrapper .remember-forgot{
    display: flex;
    justify-content: space-between;
    font-size: 14.5px;
    margin: -15px 0 15px;
}

.remember-forgot label input {
    accent-color: white;
    margin-right: 3px;
}

.remember-forgot a {
    color: white;
    text-decoration: none;
}

.remember-forgot a:hover {
    text-decoration: underline;
}

.wrapper .log-btn {
    width: 100%;
    height: 45px;
    background: white;
    border: none;
    outline: none;
    border-radius: 40px;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
    cursor: pointer;
    font-size: 16px;
    color: #333;
    font-weight: 600;
}

.wrapper .register-link{
  font-size: 14.5px;
  text-align: center;
  margin-top: 20px;
}

.register-router{
  color: white;
  text-decoration: none;
  font-weight: 600;
}

.register-router:hover{
  text-decoration: underline;
}
</style>
