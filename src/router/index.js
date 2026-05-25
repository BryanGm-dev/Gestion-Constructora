import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../components/Dashboard.vue'
import HomeView from '../views/HomeView.vue'
import GestionUsuarios from '../views/GestionUsuarios.vue'
import GestionEmpleados from '../views/GestionEmpleados.vue'
import GestionMaquinaria from '../views/GestionMaquinaria.vue'
import GestionProyectos from '../views/GestionProyectos.vue'
import GestionClientes from '@/views/GestionClientes.vue'
import LoginView from '@/views/LoginView.vue'
import MateriaPrima from '@/views/MateriaPrima.vue'


const routes = [
  {
    path: '/login',
    name: 'login',
    component: LoginView,
  },
  {
    path: '/',
    component: Dashboard, // El Dashboard será el contenedor
    meta: { requiresAuth: true }, // Requiere autenticación
    children: [
      {
        path: '',
        name: 'home',
        component: HomeView,
      },
      {
        path: 'gestion-usuarios',
        name: 'gestionUsuarios',
        component: GestionUsuarios,
      },
      {
        path: 'gestion-empleados',
        name: 'gestionEmpleados',
        component: GestionEmpleados,
      },
      {
        path: 'gestion-maquinaria',
        name: 'gestionMaquinaria',
        component: GestionMaquinaria,
      },
      {
        path: 'gestion-proyectos',
        name: 'gestionProyectos',
        component: GestionProyectos,
      },
      {
        path: 'gestion-clientes',
        name: 'gestionClientes',
        component: GestionClientes,
      },
      {
        path: 'materiaPrima',
        name: 'materiaPrima',
        component: MateriaPrima,
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
})

// Verificación de autenticación en el guard de navegación
router.beforeEach((to, from, next) => {
  const usuarioAutenticado = localStorage.getItem("usuario") !== null;

  if (to.matched.some(record => record.meta.requiresAuth) && !usuarioAutenticado) {
    next('/login');
  } else if (to.name === 'login' && usuarioAutenticado) {
    next('/');
  } else {
    next();
  }
});

export default router;
