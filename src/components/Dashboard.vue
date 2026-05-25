<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Sidebar Navigation -->
    <nav :class="{ 'w-20': !isExpanded, 'w-64': isExpanded }"
      class="bg-gray-900 h-screen fixed transition-all duration-300 shadow-lg overflow-y-auto">
      <!-- Toggle Button -->
      <div @click="toggleMenu"
        class="absolute top-4 right-4 text-gray-400 cursor-pointer hover:text-gray-200 transition-transform duration-300 transform"
        :class="{ 'rotate-180': isExpanded }">
        <i data-feather="chevron-right"></i>
      </div>

      <!-- Menu Sections -->
      <ul class="mt-16 space-y-6">
        <!-- Home Section -->
        <li class="px-4">
          <h2 class="text-xs text-gray-500 uppercase mb-2">Home</h2>
          <router-link to="/"
            class="flex items-center text-gray-400 hover:text-green-500 transition-colors duration-300 space-x-3 py-2 relative">
            <i data-feather="home" class="icon-spacing"></i>
            <span v-if="isExpanded" class="text-sm font-medium">Dashboard</span>
            <!-- Tooltip for collapsed menu -->
            <span v-else class="tooltip">Dashboard</span>
          </router-link>
        </li>

        <!-- Gestión Section -->
        <li class="px-4">
          <h2 class="text-xs text-gray-500 uppercase mb-2">Gestión</h2>
          <ul class="space-y-2">
            <li v-for="item in menuItems" :key="item.text">
              <router-link :to="item.link"
                class="flex items-center text-gray-400 hover:text-green-500 transition-colors duration-300 space-x-3 py-2 relative">
                <i :data-feather="item.icon" class="icon-spacing"></i>
                <span v-if="isExpanded" class="text-sm font-medium">{{ item.text }}</span>
                <!-- Tooltip for collapsed menu -->
                <span v-else class="tooltip">{{ item.text }}</span>
              </router-link>
            </li>
          </ul>
        </li>
      </ul>

      <!-- Logout Button -->
      <div class="absolute bottom-6 w-full flex justify-center">
        <button @click="logout"
          class="flex items-center w-full px-4 py-3 text-gray-400 hover:text-green-500 transition-colors duration-300 rounded-md relative">
          <i data-feather="log-out" class="icon-spacing"></i>
          <span v-if="isExpanded" class="text-sm font-medium">Cerrar Sesión</span>
          <!-- Tooltip for collapsed menu -->
          <span v-else class="tooltip">Cerrar Sesión</span>
        </button>
      </div>
    </nav>

    <!-- Main Content -->
    <div :class="{'ml-20': !isExpanded, 'ml-64': isExpanded}" class="flex-1 p-8 h-screen overflow-x-hidden transition-all duration-300">
  <router-view /> <!-- Aquí se mostrarán las vistas cargadas -->
</div>
  </div>
</template>


<script>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import feather from 'feather-icons';

export default {
  name: 'AppDashboard',
  setup() {
    const router = useRouter();
    const isExpanded = ref(true); // Por defecto, menú expandido

    const toggleMenu = () => {
      isExpanded.value = !isExpanded.value;
      feather.replace();
    };

    const logout = () => {
      localStorage.removeItem('usuario');
      router.push('/login');
    };

    onMounted(() => {
      feather.replace();
    });

    // Menú original conservado
    const menuItems = [
      { text: 'Usuarios', link: '/gestion-usuarios', icon: 'user' },
      { text: 'Empleados', link: '/gestion-empleados', icon: 'user-check' },
      { text: 'Maquinaria', link: '/gestion-maquinaria', icon: 'tool' },
      { text: 'Proyectos', link: '/gestion-proyectos', icon: 'briefcase' },
      { text: 'Clientes', link: '/gestion-clientes', icon: 'users' },
      { text: 'Materia Prima', link: '/materiaPrima', icon: 'box' },
    ];

    return {
      isExpanded,
      toggleMenu,
      logout,
      menuItems,
    };
  },
};
</script>

<style scoped>
/* Sidebar width control and general styles */
.bg-gray-900 {
  background-color: #1f2937;
}

.text-gray-500 {
  color: #6b7280;
}

.text-gray-400 {
  color: #9ca3af;
}

.hover\:text-green-500:hover {
  color: #10b981;
}

.rotate-180 {
  transform: rotate(180deg);
}

/* Title styles for sections */
h2 {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-style: semibold;
}

/* Icon spacing adjustment */
.icon-spacing {
  margin-left: 6px;
  /* Ajusta este valor según lo necesario para separar los íconos del borde izquierdo */
}

/* Tooltip styles */
.tooltip {
  position: absolute;
  left: 100%;
  top: 50%;
  transform: translateY(-50%);
  margin-left: 10px;
  padding: 6px 12px;
  /* Ajusta el tamaño del padding para un tooltip más amplio */
  background-color: #2c3a4e;
  color: #e5e7eb;
  font-size: 0.875rem;
  border-radius: 8px;
  white-space: nowrap;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease;
}

/* Show tooltip on hover */
.router-link:hover .tooltip,
button:hover .tooltip,
.router-link:focus .tooltip,
button:focus .tooltip {
  opacity: 1;
}
</style>
