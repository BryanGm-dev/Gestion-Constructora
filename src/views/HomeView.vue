<template>
  <div class="home-view">
    <h1>Panel de Administración</h1>
    <div class="views-container">
      <router-link v-for="view in views" :key="view.name" :to="view.path" class="view-card">
        <div class="card-icon">
          <span :class="view.iconClass"></span>
        </div>
        <div class="card-content">
          <h2>{{ view.title }}</h2>
          <p>{{ view.description }}</p>
          <p class="summary" v-if="view.summary !== null">{{ view.summary }}</p>
          <p class="summary loading" v-else>Cargando...</p>
        </div>
      </router-link>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'HomeView',
  data() {
    return {
      views: [
        { name: 'clientes', title: 'Gestión de Clientes', path: '/gestion-clientes', description: 'Total de Clientes', iconClass: 'fas fa-user-tie', endpoint: 'http://localhost/GestionConstructora/backend/clientes.php?action=count', summary: null },
        { name: 'empleados', title: 'Gestión de Empleados', path: '/gestion-empleados', description: 'Total de Empleados', iconClass: 'fas fa-users', endpoint: 'http://localhost/GestionConstructora/backend/empleados.php?action=count', summary: null },
        { name: 'maquinaria', title: 'Gestión de Maquinaria', path: '/gestion-maquinaria', description: 'Total de Equipos', iconClass: 'fas fa-tools', endpoint: 'http://localhost/GestionConstructora/backend/maquinaria.php?action=count', summary: null },
        { name: 'proyectos', title: 'Gestión de Proyectos', path: '/gestion-proyectos', description: 'Proyectos Activos', iconClass: 'fas fa-project-diagram', endpoint: 'http://localhost/GestionConstructora/backend/proyectos.php?action=count', summary: null },
        { name: 'usuarios', title: 'Gestión de Usuarios', path: '/gestion-usuarios', description: 'Usuarios Registrados', iconClass: 'fas fa-user-cog', endpoint: 'http://localhost/GestionConstructora/backend/usuarios.php?action=count', summary: null },
        { name: 'materiaPrima', title: 'Materia Prima', path: '/materiaPrima', description: 'Materiales en Stock', iconClass: 'fas fa-boxes', endpoint: 'http://localhost/GestionConstructora/backend/materiaPrima.php?action=count', summary: null },
      ],
    };
  },
  created() {
    this.fetchSummaries();
  },
  methods: {
    async fetchSummaries() {
      try {
        for (const view of this.views) {
          const response = await axios.get(view.endpoint);
          view.summary = response.data.total ? `${response.data.total} ${view.description.toLowerCase()}` : 'No disponible';
        }
      } catch (error) {
        console.error("Error al cargar el resumen:", error);
        this.views.forEach(view => {
          view.summary = 'Error al cargar';
        });
      }
    },
  },
};
</script>

<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');

.home-view {
  padding: 40px;
  font-family: Arial, sans-serif;
  background-color: #f4f6f9;
}

h1 {
  text-align: center;
  margin-bottom: 40px;
  color: #333;
  font-size: 2em;
  font-weight: bold;
}

.views-container {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: center;
}

.view-card {
  width: 240px;
  padding: 25px;
  text-decoration: none;
  color: inherit;
  background: linear-gradient(135deg, #ffffff 0%, #f7f9fc 100%);
  border-radius: 15px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  overflow: hidden;
}

.view-card::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(45deg, rgba(52, 58, 144, 0.1), rgba(0, 150, 136, 0.1));
  transform: rotate(30deg);
  transition: 0.5s;
}

.view-card:hover::before {
  transform: rotate(90deg);
}

.view-card:hover {
  box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
  transform: translateY(-10px);
}

.card-icon {
  background: linear-gradient(135deg, #334a69, #4caf50);
  width: 70px;
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  margin-bottom: 20px;
  color: white;
  font-size: 1.8em;
  box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
}

.card-content h2 {
  margin: 0;
  font-size: 1.2em;
  color: #333;
  text-align: center;
  font-weight: 600;
}

.card-content p {
  margin: 10px 0 0;
  font-size: 0.9em;
  color: #666;
  text-align: center;
}

.summary {
  margin-top: 10px;
  font-size: 1.1em;
  font-weight: bold;
  color: #333;
}

.loading {
  color: #999;
}
</style>
