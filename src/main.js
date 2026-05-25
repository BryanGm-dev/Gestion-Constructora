import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import './assets/tailwind.css';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import Toast from 'vue-toastification'; // Importa el plugin
import 'vue-toastification/dist/index.css'; // Importa los estilos de Toastification

window.L = L;

// Opciones para Vue Toastification (opcional)
const toastOptions = {
  position: 'top-right', // Posición del toast
  timeout: 5000,         // Duración (en ms)
  closeOnClick: true,    // Cierra el toast al hacer clic
  pauseOnHover: true,    // Pausa al pasar el cursor
  draggable: true,       // Arrastrable
  hideProgressBar: false // Muestra la barra de progreso
};

// Crea la aplicación Vue
const app = createApp(App);

// Usa los plugins
app.use(store);
app.use(router);
app.use(Toast, toastOptions); // Registra Vue Toastification con sus opciones

// Monta la aplicación
app.mount('#app');
