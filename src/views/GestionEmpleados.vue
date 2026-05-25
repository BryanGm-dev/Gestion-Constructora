<template>
  <div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
      <h2 class="text-3xl font-bold text-gray-800 mb-6">Gestión de Empleados</h2>
      <button @click="abrirModalNuevoEmpleado" style="background-color: #2c3a4e;"
        class="hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition duration-300 ease-in-out mb-8">
        Agregar Empleado
      </button>

      <!-- Filtro de búsqueda -->
      <div class="mb-4">
        <input v-model="filtro" type="text" placeholder="Buscar por nombre o email"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
      </div>

      <!-- Tabla de Empleados -->
      <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto overflow-y-auto" style="max-height: 400px;">
          <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Nombre</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Puesto</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Salario</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Email</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Teléfono</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Acciones</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="empleado in empleadosFiltrados" :key="empleado.empleado_id"
              class="hover:bg-gray-50 transition-colors duration-200 cursor-pointer" @click="verEmpleado(empleado)">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ empleado.nombre }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getPuestoNombre(empleado.puesto_id) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ empleado.salario.toLocaleString() }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ empleado.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ empleado.telefonos ? empleado.telefonos.split(',')[0] : 'No registrado' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button @click.stop="editarEmpleado(empleado)"
                  class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-1 px-2 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                  Editar
                </button>
                <button @click.stop="eliminarEmpleado(empleado.empleado_id)"
                  class="bg-red-500 hover:bg-red-700 text-white font-semibold py-1 px-2 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                  Eliminar
                </button>
                <button @click.stop="abrirModalTelefono(empleado.empleado_id)"
                  class="bg-green-500 hover:bg-green-700 text-white font-semibold py-1 px-2 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                  Añadir Teléfono
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        </div>
      </div>

      <!-- Modal para Crear/Editar Empleado -->
      <div v-if="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-2xl font-semibold text-gray-800">{{ empleadoEdit ? "Editar Empleado" : "Nuevo Empleado" }}
            </h3>
          </div>
          <form @submit.prevent="submitEmpleado" class="p-6">
            <div class="mb-4">
              <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
              <input id="nombre" v-model="empleadoData.nombre" type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                required />
            </div>

            <div class="mb-4">
              <label for="puesto" class="block text-sm font-medium text-gray-700 mb-1">Puesto</label>
              <select id="puesto" v-model="empleadoData.puesto_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                required>
                <option v-for="puesto in puestos" :key="puesto.puesto_id" :value="puesto.puesto_id">{{ puesto.nombre }}
                </option>
              </select>
            </div>

            <div class="mb-4">
              <label for="salario" class="block text-sm font-medium text-gray-700 mb-1">Salario</label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                <input id="salario" v-model="empleadoData.salario" type="number"
                  class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  required />
              </div>
            </div>

            <div class="mb-6">
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input id="email" v-model="empleadoData.email" type="email"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                required />
            </div>

            <div class="mb-6">
              <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
              <input id="telefono" v-model="empleadoData.telefono" type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <div class="flex justify-end space-x-3">
              <button type="button" @click="cerrarModal"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                Cancelar
              </button>
              <button type="submit"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                {{ empleadoEdit ? "Actualizar" : "Crear" }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <div v-if="mostrarModalEmpleado"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl p-6">
          <!-- max-w-lg cambiado a max-w-2xl para mayor ancho -->
          <!-- Header del Modal -->
          <div class="flex items-center justify-between border-b pb-4 mb-4">
            <h3 class="text-2xl font-bold text-gray-800">Información del Empleado</h3>
            <button @click="mostrarModalEmpleado = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Contenido del Modal -->
          <div class="space-y-6">
            <!-- Información General -->
            <div class="bg-gray-50 p-4 rounded-lg grid grid-cols-2 gap-4">
              <!-- Nombre -->
              <div>
                <p class="text-sm font-semibold text-gray-500 uppercase">Nombre</p>
                <p class="text-lg font-medium text-gray-800">{{ empleadoSeleccionado.nombre }}</p>
              </div>

              <!-- Puesto -->
              <div>
                <p class="text-sm font-semibold text-gray-500 uppercase">Puesto</p>
                <p class="text-lg font-medium text-gray-800">{{ getPuestoNombre(empleadoSeleccionado.puesto_id) }}</p>
              </div>

              <!-- Salario -->
              <div>
                <p class="text-sm font-semibold text-gray-500 uppercase">Salario</p>
                <p class="text-lg font-medium text-gray-800">${{ empleadoSeleccionado.salario.toLocaleString() }}</p>
              </div>

              <!-- Email (ocupando toda la fila) -->
              <div class="col-span-">
                <p class="text-sm font-semibold text-gray-500 uppercase">Email</p>
                <p class="text-lg font-medium text-gray-800">{{ empleadoSeleccionado.email }}</p>
              </div>
            </div>

            <!-- Lista de Teléfonos -->
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-sm font-semibold text-gray-500 uppercase mb-2">Teléfonos</p>
              <ul class="space-y-2">
                <li
                  v-for="telefono in (empleadoSeleccionado.telefonos ? empleadoSeleccionado.telefonos.split(',') : [])"
                  :key="telefono" class="flex items-center space-x-2">
                  <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 10h2l1-2 1.4-2.4c.5-.9 1.7-.9 2.1 0L12 10h4l1-2 1.4-2.4c.5-.9 1.7-.9 2.1 0L21 8h2"></path>
                  </svg>
                  <span class="text-gray-800 text-md">{{ telefono }}</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- Footer del Modal -->
          <div class="mt-6 flex justify-end">
            <button @click="mostrarModalEmpleado = false"
              class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
              Cerrar
            </button>
          </div>
        </div>
      </div>


      <!-- Modal para Añadir Teléfono -->
      <div v-if="mostrarModalTelefono"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-2xl font-semibold text-gray-800">Añadir Teléfono</h3>
          </div>
          <form @submit.prevent="agregarTelefono" class="p-6">
            <div class="mb-6">
              <label for="nuevoTelefono" class="block text-sm font-medium text-gray-700 mb-1">Número de Teléfono</label>
              <input id="nuevoTelefono" v-model="nuevoTelefono" type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                required />
            </div>
            <div class="flex justify-end space-x-3">
              <button type="button" @click="mostrarModalTelefono = false"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                Cancelar
              </button>
              <button type="submit"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                Añadir Teléfono
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      empleados: [],
      puestos: [],
      empleadoData: {
        nombre: '',
        puesto_id: null,
        salario: 0,
        email: '',
        telefono: '', // Teléfono principal
      },
      mostrarModal: false,
      mostrarModalEmpleado: false,
      mostrarModalTelefono: false,
      empleadoEdit: false,
      empleadoSeleccionado: {}, // Para detalles del empleado
      empleadoIdTelefono: null, // Para añadir teléfono
      nuevoTelefono: '', // Para nuevo número de teléfono
      filtro: '', // Filtro de búsqueda
    };
  },
  computed: {
    empleadosFiltrados() {
      return this.empleados.filter(empleado => {
        const filtro = this.filtro.toLowerCase();
        return (
          empleado.nombre.toLowerCase().includes(filtro) ||
          empleado.email.toLowerCase().includes(filtro)
        );
      });
    },
  },
  methods: {
    obtenerEmpleados() {
      axios.get('http://localhost/GestionConstructora/backend/empleados.php')
        .then(response => {
          this.empleados = response.data;
        })
        .catch(error => console.error("Error al obtener empleados:", error));
    },
    abrirModalNuevoEmpleado() {
      this.resetFormulario();
      this.mostrarModal = true;
    },
    submitEmpleado() {
      if (this.empleadoEdit) {
        this.actualizarEmpleado();
      } else {
        this.crearEmpleado();
      }
      this.mostrarModal = false;
    },
    crearEmpleado() {
      axios.post('http://localhost/GestionConstructora/backend/empleados.php', this.empleadoData)
        .then(response => {
          console.log("Empleado creado:", response.data);
          this.obtenerEmpleados();
          this.resetFormulario();
        })
        .catch(error => console.error("Error al crear empleado:", error));
    },
    editarEmpleado(empleado) {
      this.empleadoData = { ...empleado, telefono: '' };
      this.empleadoEdit = true;
      this.mostrarModal = true;
    },
    actualizarEmpleado() {
      axios.put('http://localhost/GestionConstructora/backend/empleados.php', this.empleadoData)
        .then(response => {
          console.log("Empleado actualizado:", response.data);
          this.obtenerEmpleados();
          this.resetFormulario();
        })
        .catch(error => console.error("Error al actualizar empleado:", error));
    },
    eliminarEmpleado(id) {
      axios.delete('http://localhost/GestionConstructora/backend/empleados.php', { data: { empleado_id: id } })
        .then(response => {
          console.log("Empleado eliminado:", response.data);
          this.obtenerEmpleados();
        })
        .catch(error => console.error("Error al eliminar empleado:", error));
    },
    abrirModalTelefono(empleado_id) {
      this.empleadoIdTelefono = empleado_id;
      this.nuevoTelefono = '';
      this.mostrarModalTelefono = true;
    },
    agregarTelefono() {
      axios.post('http://localhost/GestionConstructora/backend/empleados.php?action=add_phone', {
        empleado_id: this.empleadoIdTelefono,
        numero_telefono: this.nuevoTelefono,
      })
        .then(response => {
          console.log("Teléfono agregado:", response.data);
          this.obtenerEmpleados();
          this.mostrarModalTelefono = false;
        })
        .catch(error => console.error("Error al agregar teléfono:", error));
    },
    verEmpleado(empleado) {
      this.empleadoSeleccionado = empleado;
      this.mostrarModalEmpleado = true;
    },
    resetFormulario() {
      this.empleadoData = {
        nombre: '',
        puesto_id: null,
        salario: 0,
        email: '',
        telefono: '',
      };
      this.empleadoEdit = false;
    },
    cerrarModal() {
      this.mostrarModal = false;
      this.resetFormulario();
    },
    getPuestoNombre(puesto_id) {
      const puesto = this.puestos.find(p => p.puesto_id === puesto_id);
      return puesto ? puesto.nombre : '';
    },
  },
  mounted() {
    this.obtenerEmpleados();
    // Asume que los puestos están disponibles en algún endpoint
    axios.get('http://localhost/GestionConstructora/backend/puestos.php')
      .then(response => {
        this.puestos = response.data;
      })
      .catch(error => console.error("Error al obtener puestos:", error));
  },
};
</script>
