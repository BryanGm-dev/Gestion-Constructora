<template>
    <div class="flex h-screen bg-gray-100">
      <!-- Left panel -->
      <div class="w-3/5 h-full flex flex-col justify-center p-8 bg-white">
        <div class="mb-10 text-center"> <!-- Ajuste de centrado y margen superior -->
          <h2 class="text-3xl font-semibold text-gray-700 mb-2">BIENVENID@</h2>
          <h1 class="text-5xl font-bold text-[#334155]">Terranova Projects</h1>
        </div>
  
        <!-- Mensaje de error en rojo si las credenciales son incorrectas -->
        <p v-if="mensaje" class="text-center text-red-500 mb-4">{{ mensaje }}</p>
  
        <form @submit.prevent="iniciarSesion" class="space-y-4">
          <div class="relative">
            <!-- Campo de usuario o correo -->
            <input 
              type="text" 
              placeholder="Enter your email or username" 
              class="w-full pr-10 pl-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
              v-model="login">
            <span class="absolute inset-y-0 right-0 flex items-center pr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
            </span>
          </div>
          <div class="relative">
            <!-- Campo de contraseña -->
            <input 
              type="password" 
              placeholder="Password" 
              class="w-full pr-10 pl-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
              v-model="password">
            <span class="absolute inset-y-0 right-0 flex items-center pr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="16" r="1"/>
                <rect x="3" y="10" width="18" height="12" rx="2"/>
                <path d="M7 10V7a5 5 0 0 1 10 0v3"/>
              </svg>
            </span>
          </div>
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
              <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-red-500 focus:ring-red-500">
              <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                Remember Me
              </label>
            </div>
          </div>
          <button type="submit" class="w-full bg-[#334155] text-white py-3 rounded-md hover:bg-[#1e293b] transition duration-300">Iniciar Sesion</button>
        </form>
  
        <p class="text-center mt-8 text-sm text-gray-600">
          Copyright © 2024 TerranovaProjects, S.A. DE C.V.
        </p>
      </div>
  
      <!-- Right panel -->
      <div class="w-2/5 h-full relative">
        <img src="../assets/fondo.jpg" alt="Background Image" class="absolute inset-0 h-full w-full object-cover opacity-60"> <!-- Reducir opacidad de la imagen -->
        <div class="relative z-10 h-full flex flex-col justify-center items-center px-8 bg-[#1E2A38] bg-opacity-80"> <!-- Ajuste de opacidad del fondo -->
          <h1 class="text-5xl font-bold text-white">TERRANOVA</h1>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from "axios";
  
  export default {
    data() {
      return {
        login: "",      // nombre de usuario o correo electrónico
        password: "",   // contraseña
        mensaje: ""     // mensaje de error o éxito
      };
    },
    methods: {
      iniciarSesion() {
        axios.post("http://localhost/GestionConstructora/backend/login.php", {
          login: this.login,
          password: this.password
        })
        .then(response => {
          if (response.data.error) {
            this.mensaje = response.data.error;
          } else {
            this.mensaje = response.data.message;
            localStorage.setItem("usuario", JSON.stringify(response.data.user));
            this.$router.push("/"); // Redirige al Dashboard
          }
        })
        .catch(error => {
          console.error("Error en el inicio de sesión:", error);
          this.mensaje = "Error al conectar con el servidor.";
        });
      }
    }
  };
  </script>
  