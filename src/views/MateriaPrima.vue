<template>
    <div class="min-h-screen bg-gray-100 p-8">
      <div class="max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Gestión de Materia Prima</h2>
        <button @click="abrirModalNuevaMateriaPrima" style="background-color: #2c3a4e;"
          class="text-white font-semibold px-6 py-3 rounded-lg shadow-md transition duration-300 ease-in-out mb-8">
          Agregar Materia Prima
        </button>
  
        <div class="mb-4">
          <input v-model="filtro" type="text" placeholder="Buscar por nombre"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
        </div>
  
        <!-- Tabla de Materia Prima -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
          <div class="overflow-x-auto overflow-y-auto" style="max-height: 400px;">
            <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo Unitario</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad en Stock</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="material in materialesFiltrados" :key="material.materia_prima_id" class="hover:bg-gray-50 transition-colors duration-200">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ material.nombre }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ material.descripcion }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  ${{ (material.costo_unitario || 0).toFixed(2) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ material.cantidad_en_stock }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                  <button @click.stop="editarMaterial(material)" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-1 px-2 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                    Editar
                  </button>
                  <button @click.stop="eliminarMaterial(material.materia_prima_id)" class="bg-red-500 hover:bg-red-700 text-white font-semibold py-1 px-2 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                    Eliminar
                  </button>
                </div>
              </td>
              </tr>
            </tbody>
          </table>
          </div>
        </div>
  
        <!-- Modal para Crear/Editar Materia Prima -->
        <div v-if="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-2xl font-semibold text-gray-800">{{ materialEdit ? "Editar Materia Prima" : "Nueva Materia Prima" }}</h3>
            </div>
            <form @submit.prevent="submitMaterial" class="p-6">
              <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input id="nombre" v-model="materialData.nombre" type="text"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  required />
              </div>
  
              <div class="mb-4">
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea id="descripcion" v-model="materialData.descripcion"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
              </div>
  
              <div class="mb-4">
                <label for="costo_unitario" class="block text-sm font-medium text-gray-700 mb-1">Costo Unitario</label>
                <input id="costo_unitario" v-model="materialData.costo_unitario" type="number" step="0.01"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  required />
              </div>
  
              <div class="mb-6">
                <label for="cantidad_en_stock" class="block text-sm font-medium text-gray-700 mb-1">Cantidad en Stock</label>
                <input id="cantidad_en_stock" v-model="materialData.cantidad_en_stock" type="number"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                  required />
              </div>
  
              <div class="flex justify-end space-x-3">
                <button type="button" @click="cerrarModal"
                  class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Cancelar</button>
                <button type="submit"
                  class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                  {{ materialEdit ? "Actualizar" : "Crear" }}
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
        materiales: [],
        materialData: {
          nombre: '',
          descripcion: '',
          costo_unitario: 0,
          cantidad_en_stock: 0
        },
        mostrarModal: false,
        materialEdit: false,
        filtro: ''
      };
    },
    computed: {
      materialesFiltrados() {
        return this.materiales.filter(material =>
          material.nombre.toLowerCase().includes(this.filtro.toLowerCase())
        );
      }
    },
    methods: {
      obtenerMateriales() {
        axios.get('http://localhost/GestionConstructora/backend/materiaPrima.php')
          .then(response => {
            this.materiales = response.data;
          })
          .catch(error => console.error("Error al obtener materiales:", error));
      },
      abrirModalNuevaMateriaPrima() {
        this.resetFormulario();
        this.mostrarModal = true;
      },
      submitMaterial() {
        if (this.materialEdit) {
          this.actualizarMaterial();
        } else {
          this.crearMaterial();
        }
      },
      crearMaterial() {
        axios.post('http://localhost/GestionConstructora/backend/materiaPrima.php?action=add_material', this.materialData)
          .then(response => {
            console.log("Materia prima creada:", response.data);
            this.obtenerMateriales();
            this.cerrarModal();
          })
          .catch(error => console.error("Error al crear materia prima:", error));
      },
      editarMaterial(material) {
        this.materialData = { ...material };
        this.materialEdit = true;
        this.mostrarModal = true;
      },
      actualizarMaterial() {
        axios.put('http://localhost/GestionConstructora/backend/materiaPrima.php', this.materialData)
          .then(response => {
            console.log("Materia prima actualizada:", response.data);
            this.obtenerMateriales();
            this.cerrarModal();
          })
          .catch(error => console.error("Error al actualizar materia prima:", error));
      },
      eliminarMaterial(id) {
        axios.delete('http://localhost/GestionConstructora/backend/materiaPrima.php', { data: { materia_prima_id: id } })
          .then(response => {
            console.log("Materia prima eliminada:", response.data);
            this.obtenerMateriales();
          })
          .catch(error => console.error("Error al eliminar materia prima:", error));
      },
      cerrarModal() {
        this.mostrarModal = false;
        this.resetFormulario();
      },
      resetFormulario() {
        this.materialData = {
          nombre: '',
          descripcion: '',
          costo_unitario: 0,
          cantidad_en_stock: 0
        };
        this.materialEdit = false;
      }
    },
    mounted() {
      this.obtenerMateriales();
    }
  };
  </script>
  