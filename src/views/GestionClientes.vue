<template>
  <div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto">
      <h2 class="text-3xl font-bold text-gray-800 mb-6">Gestión de Clientes</h2>
      <button @click="abrirModalNuevoCliente" style="background-color: #2c3a4e;"
        class="text-white font-semibold px-6 py-3 rounded-lg shadow-md transition duration-300 ease-in-out mb-8">
        Agregar Cliente
      </button>

      <div class="mb-4">
        <input v-model="filtro" type="text" placeholder="Buscar por nombre o email"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
      </div>

      <!-- Tabla de Clientes -->
      <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Nombre</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Dirección</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Email</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Teléfono</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Acciones</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="cliente in clientesFiltrados" :key="cliente.cliente_id"
              class="hover:bg-gray-50 transition-colors duration-200 cursor-pointer" @click="verCliente(cliente)">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ cliente.nombre }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ cliente.direccion }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ cliente.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ cliente.telefonos.length > 0 ? cliente.telefonos[0] : 'No registrado' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                  <button @click.stop="editarCliente(cliente)"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-1 px-2 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                    Editar
                  </button>
                  <button @click.stop="eliminarCliente(cliente.cliente_id)"
                    class="bg-red-500 hover:bg-red-700 text-white font-semibold py-1 px-2 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                    Eliminar
                  </button>
                  <button @click.stop="abrirModalTelefono(cliente.cliente_id)"
                    class="bg-green-500 hover:bg-green-700 text-white font-semibold py-1 px-2 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                    Añadir Teléfono
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal para Crear/Editar Cliente -->
      <div v-if="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-2xl font-semibold text-gray-800">{{ clienteEdit ? "Editar Cliente" : "Nuevo Cliente" }}</h3>
          </div>
          <form @submit.prevent="submitCliente" class="p-6">
            <div class="mb-4">
              <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
              <input id="nombre" v-model="clienteData.nombre" type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                required />
            </div>

            <div class="mb-4">
              <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
              <input id="direccion" v-model="clienteData.direccion" type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <div class="mb-4">
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input id="email" v-model="clienteData.email" type="email"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <div class="mb-6">
              <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
              <input id="telefono" v-model="clienteData.telefono" type="text"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <div class="flex justify-end space-x-3">
              <button type="button" @click="cerrarModal"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                Cancelar
              </button>
              <button type="submit"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                {{ clienteEdit ? "Actualizar" : "Crear" }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <div v-if="mostrarModalCliente"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg p-6">
          <!-- Header del Modal -->
          <div class="flex items-center justify-between border-b pb-4 mb-4">
            <h3 class="text-2xl font-bold text-gray-800">Información del Cliente</h3>
            <button @click="mostrarModalCliente = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Contenido del Modal -->
          <div class="space-y-6">
            <!-- Información General -->
            <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
              <div>
                <p class="text-sm font-semibold text-gray-500 uppercase">Nombre</p>
                <p class="text-lg font-medium text-gray-800">{{ clienteSeleccionado.nombre }}</p>
              </div>

              <div>
                <p class="text-sm font-semibold text-gray-500 uppercase">Dirección</p>
                <p class="text-lg font-medium text-gray-800">{{ clienteSeleccionado.direccion }}</p>
              </div>

              <div>
                <p class="text-sm font-semibold text-gray-500 uppercase">Email</p>
                <p class="text-lg font-medium text-gray-800">{{ clienteSeleccionado.email }}</p>
              </div>
            </div>

            <!-- Lista de Teléfonos -->
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-sm font-semibold text-gray-500 uppercase mb-2">Teléfonos</p>
              <ul class="space-y-2">
                <li v-for="telefono in clienteSeleccionado.telefonos" :key="telefono"
                  class="flex items-center space-x-2">
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
            <button @click="mostrarModalCliente = false"
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
      clientes: [],
      clienteData: {
        nombre: '',
        direccion: '',
        email: '',
        telefono: '',
      },
      clienteSeleccionado: {}, // Datos del cliente seleccionado
      mostrarModal: false,
      mostrarModalCliente: false, // Modal para ver la información del cliente
      mostrarModalTelefono: false,
      clienteEdit: false,
      clienteIdTelefono: null,
      nuevoTelefono: '',
      filtro: '',
    };
  },

  computed: {
    clientesFiltrados() {
      return this.clientes.filter(cliente => {
        const filtro = this.filtro.toLowerCase();
        return (
          cliente.nombre.toLowerCase().includes(filtro) ||
          cliente.email.toLowerCase().includes(filtro)
        );
      });
    }
  },

  methods: {
    obtenerClientes() {
      axios.get('http://localhost/GestionConstructora/backend/clientes.php')
        .then(response => {
          this.clientes = response.data;
        })
        .catch(error => console.error("Error al obtener clientes:", error));
    },
    abrirModalNuevoCliente() {
      this.resetFormulario();
      this.mostrarModal = true;
    },
    submitCliente() {
      if (this.clienteEdit) {
        this.actualizarCliente();
      } else {
        this.crearCliente();
      }
      this.mostrarModal = false;
    },
    crearCliente() {
      axios.post('http://localhost/GestionConstructora/backend/clientes.php', this.clienteData)
        .then(response => {
          console.log("Cliente creado:", response.data);
          this.obtenerClientes();
          this.resetFormulario();
          this.mostrarModal = false;
        })
        .catch(error => console.error("Error al crear cliente:", error));
    },
    editarCliente(cliente) {
      this.clienteData = { ...cliente, telefono: '' }; // Limpiar campo de teléfono para evitar confusión
      this.clienteEdit = true;
      this.mostrarModal = true;
    },
    actualizarCliente() {
      axios.put('http://localhost/GestionConstructora/backend/clientes.php', this.clienteData)
        .then(response => {
          console.log("Cliente actualizado:", response.data);
          this.obtenerClientes();
          this.resetFormulario();
        })
        .catch(error => console.error("Error al actualizar cliente:", error));
    },
    eliminarCliente(id) {
      axios.delete('http://localhost/GestionConstructora/backend/clientes.php', { data: { cliente_id: id } })
        .then(response => {
          console.log("Cliente eliminado:", response.data);
          this.obtenerClientes();
        })
        .catch(error => console.error("Error al eliminar cliente:", error));
    },
    abrirModalTelefono(cliente_id) {
      this.clienteIdTelefono = cliente_id;
      this.nuevoTelefono = ''; // Resetear el campo de nuevo teléfono
      this.mostrarModalTelefono = true;
    },
    agregarTelefono() {
      axios.post('http://localhost/GestionConstructora/backend/clientes.php?action=add_phone', {
        cliente_id: this.clienteIdTelefono,
        numero_telefono: this.nuevoTelefono,
      })
        .then(response => {
          console.log("Teléfono agregado:", response.data);
          this.obtenerClientes(); // Refresca la lista para mostrar el nuevo teléfono
          this.mostrarModalTelefono = false;
          this.nuevoTelefono = '';
        })
        .catch(error => console.error("Error al agregar teléfono:", error));
    },
    verCliente(cliente) {
      this.clienteSeleccionado = cliente; // Asigna el cliente seleccionado para ver la información
      this.mostrarModalCliente = true;
    },
    resetFormulario() {
      this.clienteData = {
        nombre: '',
        direccion: '',
        email: '',
        telefono: '',
      };
      this.clienteEdit = false;
    },
    cerrarModal() {
      this.mostrarModal = false;
      this.resetFormulario();
    }
  },
  mounted() {
    this.obtenerClientes();
  },
};
</script>
