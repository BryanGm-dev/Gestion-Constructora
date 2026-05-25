<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Gestión de Maquinaria</h1>

    <!-- Filtros de búsqueda -->
    <div class="flex space-x-4 mb-4">
      <input v-model="filters.nombre" type="text" placeholder="Buscar por nombre"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
      <select v-model="filters.estado"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        <option value="">Todos los estados</option>
        <option value="Disponible">Disponible</option>
        <option value="En uso">En uso</option>
        <option value="En mantenimiento">En mantenimiento</option>
      </select>
    </div>

    <!-- Botón para abrir el modal de Ingresar Maquinaria -->
    <button @click="openModal" style="background-color: #2c3a4e;"
      class="text-white font-semibold px-4 py-2 rounded-md mb-4">
      Ingresar Maquinaria
    </button>

    <!-- Tabla de maquinaria con desplazamiento horizontal y vertical -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <div class="overflow-x-auto overflow-y-auto" style="max-height: 400px;">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Adquisición</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="item in filteredMaquinarias" :key="item.maquinaria_id"
              class="hover:bg-gray-50 transition-colors duration-200">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.maquinaria_id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.nombre }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.descripcion }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.estado }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.fecha_adquisicion }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${{ item.costo ? parseFloat(item.costo).toFixed(2) : '0.00' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.tipo }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button @click="editMaquinaria(item)"
                  class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105 mr-2">
                  Editar
                </button>
                <button @click="deleteMaquinaria(item.maquinaria_id)"
                  class="bg-red-500 hover:bg-red-700 text-white font-semibold py-1 px-3 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                  Eliminar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal para ingresar o editar maquinaria -->
    <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-lg font-bold mb-4">{{ form.maquinaria_id ? 'Editar Maquinaria' : 'Ingresar Maquinaria' }}</h2>
        <form @submit.prevent="submitForm">
          <div class="mb-4">
            <label class="block text-gray-700">Nombre</label>
            <input v-model="form.nombre" type="text" required
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Descripción</label>
            <textarea v-model="form.descripcion"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Estado</label>
            <select v-model="form.estado"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              <option value="Disponible">Disponible</option>
              <option value="En uso">En uso</option>
              <option value="En mantenimiento">En mantenimiento</option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Fecha de Adquisición</label>
            <input v-model="form.fecha_adquisicion" type="date"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Costo</label>
            <input v-model="form.costo" type="number" step="0.01"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Tipo</label>
            <input v-model="form.tipo" type="text"
              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
          </div>
          <div class="flex justify-end">
            <button @click="closeModal" type="button" class="mr-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg">Cancelar</button>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      showModal: false,
      maquinarias: [],
      filters: {
        nombre: '',
        estado: ''
      },
      form: {
        maquinaria_id: null,
        nombre: '',
        descripcion: '',
        estado: 'Disponible',
        fecha_adquisicion: '',
        costo: 0,
        tipo: '',
      },
    };
  },
  computed: {
    filteredMaquinarias() {
      return this.maquinarias.filter((maquinaria) => {
        const matchesNombre = maquinaria.nombre.toLowerCase().includes(this.filters.nombre.toLowerCase());
        const matchesEstado = this.filters.estado === '' || maquinaria.estado === this.filters.estado;
        return matchesNombre && matchesEstado;
      });
    },
  },
  methods: {
    async fetchMaquinarias() {
      try {
        const response = await axios.get('http://localhost/GestionConstructora/backend/maquinaria.php');
        this.maquinarias = response.data;
      } catch (error) {
        console.error("Error al obtener maquinarias:", error);
      }
    },
    async submitForm() {
      try {
        if (this.form.maquinaria_id) {
          await axios.put('http://localhost/GestionConstructora/backend/maquinaria.php', this.form);
        } else {
          await axios.post('http://localhost/GestionConstructora/backend/maquinaria.php', this.form);
        }
        await this.fetchMaquinarias();
        this.closeModal();
      } catch (error) {
        console.error("Error al enviar formulario:", error);
      }
    },
    editMaquinaria(item) {
      this.form = { ...item };
      this.showModal = true;
    },
    openModal() {
      this.resetForm();
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    },
    resetForm() {
      this.form = {
        maquinaria_id: null,
        nombre: '',
        descripcion: '',
        estado: 'Disponible',
        fecha_adquisicion: '',
        costo: 0,
        tipo: '',
      };
    },
    async deleteMaquinaria(id) {
      try {
        await axios.delete('http://localhost/GestionConstructora/backend/maquinaria.php', {
          data: { maquinaria_id: id },
        });
        await this.fetchMaquinarias();
      } catch (error) {
        console.error("Error al eliminar maquinaria:", error);
      }
    },
  },
  created() {
    this.fetchMaquinarias();
  },
};
</script>

<style scoped>
/* Puedes agregar estilos personalizados aquí */
</style>
