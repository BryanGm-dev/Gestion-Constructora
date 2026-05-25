<template>
  <div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900">Gestión de Proyectos</h1>
        <button @click="abrirModalCrear" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
          Crear Proyecto
        </button>
      </div>

      <!-- Tabla de Proyectos -->
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avance</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Inicio</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inversión</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="proyecto in proyectos"
              :key="proyecto.proyecto_id"
              class="hover:bg-gray-100 transition duration-150 cursor-pointer"
              @click="verDetalles(proyecto)"
            >
              <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ proyecto.nombre }}</td>
              <td class="px-6 py-4 text-sm">
                <span :class="badgeClass(proyecto.estado)" class="px-2 py-1 rounded-full">{{ estados[proyecto.estado] }}</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-700">
                <div class="w-full bg-gray-200 rounded-full h-4">
                  <div
                    class="bg-blue-600 h-4 rounded-full"
                    :style="{ width: `${proyecto.porcentaje_avance}%` }"
                  ></div>
                </div>
                <span class="text-sm text-gray-700">{{ proyecto.porcentaje_avance }}%</span>
              </td>              
              <td class="px-6 py-4 text-sm text-gray-700">{{ proyecto.fecha_inicio }}</td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ formatCurrency(proyecto.inversion_inicial) }}</td>
              <td class="px-6 py-4 text-sm font-medium space-x-2">
                <button @click.stop="editarProyecto(proyecto)" class="text-blue-600 hover:text-blue-900">Editar</button>
                <button @click.stop="eliminarProyecto(proyecto.proyecto_id)" class="text-red-600 hover:text-red-900">Eliminar</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal Crear/Editar Proyecto -->
      <div v-if="mostrarModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-2xl">
          <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-2xl font-semibold text-gray-800">{{ proyectoEdit ? "Editar Proyecto" : "Nuevo Proyecto" }}</h2>
          </div>
          <div class="px-6 py-4 max-h-[calc(100vh-200px)] overflow-y-auto">
            <form @submit.prevent="submitProyecto" class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="col-span-2 space-y-2">
                <label class="block text-sm font-medium text-gray-700">Nombre del Proyecto</label>
                <input v-model="proyecto.nombre" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" required />
              </div>
              <div class="col-span-2 space-y-2">
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea v-model="proyecto.descripcion" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors"></textarea>
              </div>
              <div class="col-span-2 space-y-2">
                <label class="block text-sm font-medium text-gray-700">Imagen</label>
                <input type="file" @change="onFileChange" class="w-full" />
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Ubicación</label>
                <input v-model="proyecto.ubicacion" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" />
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Fecha de Inicio</label>
                <input v-model="proyecto.fecha_inicio" type="date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" />
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Fecha de Fin</label>
                <input v-model="proyecto.fecha_fin" type="date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" />
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <select v-model="proyecto.estado" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors">
                  <option value="NI">No Iniciado</option>
                  <option value="EE">En Ejecución</option>
                  <option value="PF">Proyecto Finalizado</option>
                </select>
              </div>
              
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Inversión Inicial</label>
                <input v-model="proyecto.inversion_inicial" type="number" min="0" step="0.01" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" />
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Inversión Final</label>
                <input v-model="proyecto.inversion_final" type="number" min="0" step="0.01" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" />
              </div>
            </form>
          </div>
          <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-end space-x-3">
            <button type="button" @click="cancelarEdicion" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
            <button type="submit" @click="submitProyecto" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">{{ proyectoEdit ? "Actualizar Proyecto" : "Crear Proyecto" }}</button>
          </div>
        </div>
      </div>
      <!-- Modal para Asignar Materia Prima -->
<div v-if="mostrarModalMateriaPrima" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-[1000]">
  <div class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-md">
    <div class="border-b border-gray-200 px-6 py-4">
      <h2 class="text-2xl font-semibold text-gray-800">Asignar Materia Prima</h2>
    </div>
    <div class="px-6 py-4">
      <form @submit.prevent="asignarMateriaPrima" class="space-y-4">
        <div class="space-y-2">
          <label for="materia_prima_id" class="block text-sm font-medium text-gray-700">Seleccionar Materia Prima</label>
          <select v-model="materiaPrima.materia_prima_id" id="materia_prima_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" required>
            <option value="" disabled>Seleccionar Materia Prima</option>
            <option v-for="item in listaMateriasPrimas" :key="item.materia_prima_id" :value="item.materia_prima_id">
              {{ item.nombre }}
            </option>
          </select>
        </div>

        <!-- Nuevo bloque para mostrar la cantidad en stock -->
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">Cantidad en Stock</label>
          <div class="w-full rounded-lg border-gray-300 shadow-sm bg-gray-100 p-2">
            {{ materiaPrimaSeleccionada.cantidad_en_stock || 'N/A' }}
          </div>
        </div>

        <!-- Resto del formulario -->
        <div class="space-y-2">
          <label for="cantidad_utilizada" class="block text-sm font-medium text-gray-700">Cantidad Utilizada</label>
          <input v-model="materiaPrima.cantidad_utilizada" type="number" id="cantidad_utilizada" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" required />
        </div>
        <div class="space-y-2">
          <label for="costo" class="block text-sm font-medium text-gray-700">Costo</label>
          <input v-model="materiaPrima.costo" type="number" id="costo" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" required />
        </div>
        <div class="space-y-2">
          <label for="fecha_utilizacion" class="block text-sm font-medium text-gray-700">Fecha de Utilización</label>
          <input v-model="materiaPrima.fecha_utilizacion" type="date" id="fecha_utilizacion" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" required />
        </div>
        <div class="flex justify-end space-x-3">
          <button type="button" @click="mostrarModalMateriaPrima = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
          <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Asignar</button>
        </div>
      </form>
    </div>
  </div>
</div>


      <!-- Modal de Detalles del Proyecto -->
<div v-if="mostrarDetalles" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
  <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">
    <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
      <h2 class="text-2xl font-semibold text-gray-800">Detalles del Proyecto</h2>
      <button @click="cerrarDetalles" class="text-gray-500 hover:text-gray-800">×</button>
    </div>

    <!-- Contenedor desplazable -->
    <div class="px-6 py-4 overflow-y-auto flex-1">
      <img
        v-if="proyectoSeleccionado.imagen"
        :src="proyectoSeleccionado.imagen"
        alt="Imagen del proyecto"
        class="w-full h-64 object-cover rounded-lg mb-4"
      />
      <div v-else class="w-full h-64 bg-gray-200 flex items-center justify-center text-gray-500 mb-4">
        <span>Sin Imagen</span>
      </div>
      
      <!-- Barra de progreso debajo de la imagen -->
      <div class="w-full bg-gray-200 rounded-full h-6 mb-4">
        <div
          class="bg-blue-600 h-6 rounded-full"
          :style="{ width: `${proyectoSeleccionado.porcentaje_avance}%` }"
        ></div>
      </div>
      <p class="text-center text-sm font-medium text-gray-700 mb-4">
        {{ proyectoSeleccionado.porcentaje_avance }}%
      </p>
      
      <!-- Detalles del proyecto -->
      <p class="text-gray-600 mb-2"><strong>Nombre:</strong> {{ proyectoSeleccionado.nombre }}</p>
      <p class="text-gray-600 mb-2"><strong>Descripción:</strong> {{ proyectoSeleccionado.descripcion }}</p>
      <p class="text-gray-600 mb-2"><strong>Ubicación:</strong> {{ proyectoSeleccionado.ubicacion }}</p>
      <p class="text-gray-600 mb-2"><strong>Fecha de Inicio:</strong> {{ proyectoSeleccionado.fecha_inicio }}</p>
      <p class="text-gray-600 mb-2"><strong>Fecha de Fin:</strong> {{ proyectoSeleccionado.fecha_fin }}</p>
      <p class="text-gray-600 mb-2"><strong>Inversión Inicial:</strong> {{ formatCurrency(proyectoSeleccionado.inversion_inicial) }}</p>
      <p class="text-gray-600 mb-2"><strong>Inversión Final:</strong> {{ formatCurrency(proyectoSeleccionado.inversion_final) }}</p>
      <p class="text-gray-600"><strong>Estado:</strong> <span :class="badgeClass(proyectoSeleccionado.estado)" class="px-2 py-1 rounded-full">{{ estados[proyectoSeleccionado.estado] }}</span></p>
      
      <!-- Lista de Tareas con botones de Editar y Eliminar -->
      <h3 class="mt-6 text-xl font-semibold text-gray-800">Tareas del Proyecto</h3>
      <div class="mt-4 max-h-48 overflow-y-auto space-y-2">
        <ul>
          <li v-for="tarea in proyectoSeleccionado.tareas" :key="tarea.tarea_id" class="flex items-center justify-between p-2 bg-gray-100 rounded-md">
            <div>
              <p class="text-sm font-medium text-gray-900">{{ tarea.nombre }}</p>
              <p class="text-sm text-gray-700">{{ tarea.descripcion }}</p>
            </div>
            <div class="flex items-center space-x-2">
              <span :class="tarea.completada ? 'text-green-500' : 'text-red-500'">
                {{ tarea.completada ? 'Completada' : 'Incompleta' }}
              </span>
              <button @click="editarTarea(tarea)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Editar</button>
              <button @click="eliminarTarea(tarea.tarea_id)" class="text-red-600 hover:text-red-800 text-sm font-medium">Eliminar</button>
            </div>
          </li>
        </ul>
      </div>

     <!-- Lista de Maquinaria con botones de Eliminar -->
      <h3 class="mt-6 text-xl font-semibold text-gray-800">Maquinaria Asignada</h3>
      <div class="mt-4 max-h-48 overflow-y-auto space-y-2">
        <ul>
          <li v-for="item in proyectoSeleccionado.maquinaria" :key="item.maquinaria_id" class="flex items-center justify-between p-2 bg-gray-100 rounded-md">
            <div>
              <p class="text-sm font-medium text-gray-900">{{ item.nombre }}</p>
              <p class="text-sm text-gray-700">{{ item.descripcion }}</p>
            </div>
            <div class="flex items-center space-x-2">
              <button @click="eliminarMaquinaria(item.proyecto_maquinaria_id)" class="text-red-600 hover:text-red-800 text-sm font-medium">Eliminar</button>
            </div>
          </li>
        </ul>
      </div>

      <!-- Botón para asignar nueva maquinaria -->
      <button @click="abrirModalMaquinaria(proyectoSeleccionado.proyecto_id)" class="px-4 py-2 mt-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
        Asignar Maquinaria
      </button>
      
      <!-- Lista de Empleados con botones de Eliminar -->
      <h3 class="mt-6 text-xl font-semibold text-gray-800">Empleados Asignados</h3>
      <div class="mt-4 max-h-48 overflow-y-auto space-y-2">
        <ul v-if="proyectoSeleccionado.empleados && proyectoSeleccionado.empleados.length > 0">
          <li v-for="item in proyectoSeleccionado.empleados" :key="item.proyecto_empleado_id" class="flex items-center justify-between p-2 bg-gray-100 rounded-md">
            <div>
              <p class="text-sm font-medium text-gray-900">{{ item.nombre }}</p>
              <p class="text-sm text-gray-700">{{ item.cargo }}</p>
              <p class="text-sm text-gray-700">Rol: {{ item.rol_en_proyecto }}</p>
            </div>
            <div class="flex items-center space-x-2">
              <button @click="eliminarEmpleado(item.proyecto_empleado_id)" class="text-red-600 hover:text-red-800 text-sm font-medium">Eliminar</button>
            </div>
          </li>
        </ul>
        <p v-else class="text-sm text-gray-500">No hay empleados asignados.</p>
      </div>

      <!-- Botón para asignar nuevo empleado -->
      <button @click="abrirModalEmpleado(proyectoSeleccionado.proyecto_id)" class="px-4 py-2 mt-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
        Asignar Empleado
      </button>


      <!-- Lista de Materia Prima con botones de Eliminar -->
      <h3 class="mt-6 text-xl font-semibold text-gray-800">Materia Prima Asignada</h3>
      <div class="mt-4 max-h-48 overflow-y-auto space-y-2">
        <ul v-if="proyectoSeleccionado.materiasPrimas && proyectoSeleccionado.materiasPrimas.length > 0">
          <li v-for="item in proyectoSeleccionado.materiasPrimas" :key="item.proyecto_materia_prima_id" class="flex items-center justify-between p-2 bg-gray-100 rounded-md">
            <div>
              <p class="text-sm font-medium text-gray-900">{{ item.nombre }}</p>
              <p class="text-sm text-gray-700">Cantidad: {{ item.cantidad_utilizada }}</p>
              <p class="text-sm text-gray-700">Costo: {{ item.costo }}</p>
              <p class="text-sm text-gray-700">Fecha de Utilización: {{ item.fecha_utilizacion }}</p>
            </div>
            <div class="flex items-center space-x-2">
              <button @click="eliminarMateriaPrima(item.proyecto_materia_prima_id)" class="text-red-600 hover:text-red-800 text-sm font-medium">Eliminar</button>
            </div>
          </li>
        </ul>
        <p v-else class="text-sm text-gray-500">No hay materias primas asignadas.</p>
      </div>


      <!-- Botón para asignar nueva materia prima -->
      <button @click="abrirModalMateriaPrima(proyectoSeleccionado.proyecto_id)" class="px-4 py-2 mt-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
        Asignar Materia Prima
      </button>


      
    </div>



    <!-- Barra de botones que se mantiene fija -->
    <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex justify-between">
      <button @click="cerrarDetalles" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cerrar</button>
      <button @click="abrirModalTarea(proyectoSeleccionado.proyecto_id)" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Agregar Tarea</button>
    </div>
  </div>
</div>

     <!-- Modal para crear tarea -->
    <div v-if="mostrarModalTarea" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-md">
        <div class="border-b border-gray-200 px-6 py-4">
          <h2 class="text-2xl font-semibold text-gray-800">Nueva Tarea</h2>
        </div>
        <div class="px-6 py-4">
          <form @submit.prevent="crearTarea" class="space-y-4">
            <div class="space-y-2">
              <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre de la Tarea</label>
              <input v-model="tarea.nombre" type="text" id="nombre" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" required />
            </div>
            <div class="space-y-2">
              <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
              <textarea v-model="tarea.descripcion" id="descripcion" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors"></textarea>
            </div>
            <div class="flex items-center space-x-2">
              <input type="checkbox" v-model="tarea.completada" id="completada" class="rounded text-blue-600 focus:ring-blue-500">
              <label for="completada" class="text-sm font-medium text-gray-700">Completada</label>
            </div>
            <div class="flex justify-end space-x-3">
              <button type="button" @click="mostrarModalTarea = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
              <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Crear Tarea</button>
            </div>
          </form>
        </div>
      </div>
    </div>
   <!-- Modal para Asignar Empleado -->
    <div v-if="mostrarModalEmpleado" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-md">
        <div class="border-b border-gray-200 px-6 py-4">
          <h2 class="text-2xl font-semibold text-gray-800">Asignar Empleado</h2>
        </div>
        <div class="px-6 py-4">
          <form @submit.prevent="asignarEmpleado" class="space-y-4">
            <div class="space-y-2">
              <label for="empleado_id" class="block text-sm font-medium text-gray-700">Seleccionar Empleado</label>
              <select v-model="empleado.empleado_id" id="empleado_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" required>
                <option value="" disabled>Seleccionar Empleado</option>
                <option v-for="item in listaEmpleados" :key="item.empleado_id" :value="item.empleado_id">
                  {{ item.nombre }}
                </option>
              </select>
            </div>
            <div class="space-y-2">
              <label for="rol_en_proyecto" class="block text-sm font-medium text-gray-700">Rol en el Proyecto</label>
              <input v-model="empleado.rol_en_proyecto" type="text" id="rol_en_proyecto" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" required />
            </div>
            <div class="space-y-2">
              <label for="fecha_asignacion" class="block text-sm font-medium text-gray-700">Fecha de Asignación</label>
              <input v-model="empleado.fecha_asignacion" type="date" id="fecha_asignacion" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" required />
            </div>
            <div class="space-y-2">
              <label for="fecha_liberacion" class="block text-sm font-medium text-gray-700">Fecha de Liberación</label>
              <input v-model="empleado.fecha_liberacion" type="date" id="fecha_liberacion" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" />
            </div>
            <div class="flex justify-end space-x-3">
              <button type="button" @click="mostrarModalEmpleado = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
              <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Asignar</button>
            </div>
          </form>
        </div>
      </div>
    </div>


    <!-- Modal para Asignar Maquinaria -->
    <div v-if="mostrarModalMaquinaria" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-md">
        <div class="border-b border-gray-200 px-6 py-4">
          <h2 class="text-2xl font-semibold text-gray-800">Asignar Maquinaria</h2>
        </div>
        <div class="px-6 py-4">
          <form @submit.prevent="asignarMaquinaria" class="space-y-4">
            <div class="space-y-2">
              <label for="maquinaria_id" class="block text-sm font-medium text-gray-700">Seleccionar Maquinaria</label>
              <select v-model="maquinaria.maquinaria_id" id="maquinaria_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" required>
                <option value="" disabled>Seleccionar Maquinaria</option>
                <option v-for="item in listaMaquinaria" :key="item.maquinaria_id" :value="item.maquinaria_id">
                  {{ item.nombre }}
                </option>
              </select>
            </div>
            <div class="space-y-2">
              <label for="fecha_asignacion" class="block text-sm font-medium text-gray-700">Fecha de Asignación</label>
              <input v-model="maquinaria.fecha_asignacion" type="date" id="fecha_asignacion" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" required />
            </div>
            <div class="space-y-2">
              <label for="fecha_liberacion" class="block text-sm font-medium text-gray-700">Fecha de Liberación</label>
              <input v-model="maquinaria.fecha_liberacion" type="date" id="fecha_liberacion" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" />
            </div>
            <div class="flex justify-end space-x-3">
              <button type="button" @click="mostrarModalMaquinaria = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
              <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Asignar</button>
            </div>
          </form>
        </div>
      </div>
</div>


    <!-- Modal para editar tarea -->
    <div v-if="mostrarModalEditarTarea" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white rounded-lg shadow-xl overflow-hidden w-full max-w-md">
        <div class="border-b border-gray-200 px-6 py-4">
          <h2 class="text-2xl font-semibold text-gray-800">Editar Tarea</h2>
        </div>
        <div class="px-6 py-4">
          <form @submit.prevent="actualizarTarea" class="space-y-4">
            <div class="space-y-2">
              <label for="nombre-editar" class="block text-sm font-medium text-gray-700">Nombre de la Tarea</label>
              <input v-model="tareaSeleccionada.nombre" type="text" id="nombre-editar" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors" required />
            </div>
            <div class="space-y-2">
              <label for="descripcion-editar" class="block text-sm font-medium text-gray-700">Descripción</label>
              <textarea v-model="tareaSeleccionada.descripcion" id="descripcion-editar" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors"></textarea>
            </div>
            <div class="flex items-center space-x-2">
              <input type="checkbox" v-model="tareaSeleccionada.completada" id="completada-editar" class="rounded text-blue-600 focus:ring-blue-500">
              <label for="completada-editar" class="text-sm font-medium text-gray-700">Completada</label>
            </div>
            <div class="flex justify-end space-x-3">
              <button type="button" @click="mostrarModalEditarTarea = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
              <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Guardar Cambios</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>
</div>
</template>

<script>
import { useToast } from "vue-toastification";

import axios from "axios";

export default {
  data() {
    return {
      proyectos: [],
      proyecto: {
        nombre: "",
        descripcion: "",
        ubicacion: "",
        fecha_inicio: "",
        fecha_fin: "",
        estado: "NI",
        porcentaje_avance: 0,
        inversion_inicial: 0,
        inversion_final: 0,
        imagen: ""
      },
      proyectoSeleccionado: {},
      tarea: {
        proyecto_id: null,
        nombre: "",
        descripcion: "",
        completada: false
      },
      estados: {
        NI: "No Iniciado",
        EE: "En Ejecución",
        PF: "Proyecto Finalizado"
      },
      maquinaria: {
        proyecto_id: null,
        maquinaria_id: null,
        fecha_asignacion: new Date().toISOString().substr(0, 10), // Fecha actual
        fecha_liberacion: ""
      },
      empleado: {
        proyecto_id: null,
        empleado_id: null,
        fecha_asignacion: new Date().toISOString().substr(0, 10),
        fecha_liberacion: ""
      },
      materiaPrima: {
        proyecto_id: null,
        materia_prima_id: null,
        cantidad_utilizada: null,
        costo: null,
        fecha_utilizacion: new Date().toISOString().substr(0, 10)
      },
      empleados: [],
      listaMateriasPrimas: [], // Lista de materias primas disponibles
      mostrarModalMateriaPrima: false,
      listaEmpleados: [], // Lista de empleados disponibles
      mostrarModalEmpleado: false, // Controla el modal de asignación de empleados
      listaMaquinaria: [], // Aquí se guardará la maquinaria disponible
      maquinariaSeleccionada: {}, // Maquinaria actualmente seleccionada para editar
      mostrarModalMaquinaria: false, // Controla el modal de asignación de maquinaria
      proyectoEdit: false,
      mostrarModal: false,
      mostrarDetalles: false,
      mostrarModalTarea: false, // <- Falta una coma aquí
      tareaSeleccionada: {}, // La tarea actualmente seleccionada para editar
      mostrarModalEditarTarea: false // Controla la visibilidad del modal de edición
    };
  },
  computed: {
    materiaPrimaSeleccionada() {
      return this.listaMateriasPrimas.find(
        item => item.materia_prima_id == this.materiaPrima.materia_prima_id
      ) || {};
    }
  },

  setup() {
    const toast = useToast(); // Obtén el sistema de toasts

    return { toast }; // Devuelve el toast para usarlo en los métodos
  },
  methods: {

    // Método para abrir el modal de asignación de materia prima
    abrirModalMateriaPrima(proyectoId) {
      this.materiaPrima = {
        proyecto_id: proyectoId,
        materia_prima_id: null,
        cantidad_utilizada: null,
        costo: null,
        fecha_utilizacion: new Date().toISOString().substr(0, 10)
      };
      this.obtenerMateriasPrimas(); // Cargar la lista de materias primas disponibles
      this.mostrarModalMateriaPrima = true;
    },


    // Método para obtener la lista de materias primas
    async obtenerMateriasPrimas() {
  try {
    const response = await axios.get("http://localhost/GestionConstructora/backend/materias_primas.php");
    if (Array.isArray(response.data)) {
      // Convertir materia_prima_id a number
      this.listaMateriasPrimas = response.data.map(item => {
        return {
          ...item,
          materia_prima_id: Number(item.materia_prima_id),
          cantidad_en_stock: Number(item.cantidad_en_stock)
        };
      });
      console.log("Materias primas cargadas:", this.listaMateriasPrimas);
    } else {
      console.error("Error: Se esperaba un array en response.data, pero se recibió:", response.data);
    }
  } catch (error) {
    console.error("Error al obtener materias primas:", error);
  }
},


    async asignarMateriaPrima() {
      if (
        !this.materiaPrima.proyecto_id ||
        !this.materiaPrima.materia_prima_id ||
        !this.materiaPrima.cantidad_utilizada ||
        !this.materiaPrima.costo
      ) {
        alert("Por favor completa todos los campos requeridos para asignar materia prima.");
        return;
      }

      try {
    const response = await axios.post("http://localhost/GestionConstructora/backend/proyectos_materia_prima.php", this.materiaPrima, {
      headers: { "Content-Type": "application/json" },
    });

    if (response.data.message) {
      console.log("Materia Prima asignada correctamente");

      // Refresca toda la lista de proyectos para ver la inversión actualizada
      await this.obtenerProyectos();
      await this.obtenerMateriasPrimasAsignadas(this.materiaPrima.proyecto_id);

      // Cerrar el modal y resetear el formulario
      this.mostrarModalMateriaPrima = false;
      this.materiaPrima = {
        proyecto_id: null,
        materia_prima_id: null,
        cantidad_utilizada: null,
        costo: null,
        fecha_utilizacion: new Date().toISOString().substr(0, 10),
      };
    }
  } catch (error) {
    console.error("Error al asignar materia prima:", error);
  }
},

async actualizarDetallesProyecto(proyecto_id) {
  try {
    const response = await axios.get(
      `http://localhost/GestionConstructora/backend/proyectos.php?proyecto_id=${proyecto_id}`,
      {
        headers: { "Content-Type": "application/json" },
      }
    );

    if (response.data) {
      // Actualiza el proyecto seleccionado con los datos más recientes
      this.detallesProyecto = response.data;
      console.log("Detalles del proyecto actualizados:", this.detallesProyecto);
    }
  } catch (error) {
    console.error("Error al actualizar los detalles del proyecto:", error);
  }
},

sincronizarListaProyectos(proyectoActualizado) {
  const index = this.proyectos.findIndex(
    (proyecto) => proyecto.proyecto_id === proyectoActualizado.proyecto_id
  );

  if (index !== -1) {
    this.$set(this.proyectos, index, proyectoActualizado); // Esto forzará la reactividad en Vue 2
  } else {
    // Si el proyecto no existe en la lista, lo puedes agregar
    this.proyectos.push(proyectoActualizado);
  }
},



    // Método para obtener materias primas asignadas a un proyecto
    async obtenerMateriasPrimasAsignadas(proyecto_id) {
      try {
        const response = await axios.get(`http://localhost/GestionConstructora/backend/proyectos_materia_prima.php?proyecto_id=${proyecto_id}`);
        if (Array.isArray(response.data)) {
          this.proyectoSeleccionado.materiasPrimas = response.data;
          console.log("Materias primas asignadas:", this.proyectoSeleccionado.materiasPrimas); // Verifica los datos en la consola
        } else {
          console.error("Error: Se esperaba un array en response.data, pero se recibió:", response.data);
        }
      } catch (error) {
        console.error("Error al obtener materias primas asignadas:", error);
      }
    },

    // Método para eliminar materia prima
    async eliminarMateriaPrima(proyecto_materia_prima_id) {
      if (confirm("¿Estás seguro de que deseas eliminar esta materia prima?")) {
        try {
          const response = await axios.post(
            "http://localhost/GestionConstructora/backend/proyectos_materia_prima.php",
            {
              proyecto_materia_prima_id: proyecto_materia_prima_id,
              _method: "DELETE"
            },
            {
              headers: { "Content-Type": "application/json" }
            }
          );

          if (response.data.message) {
            console.log("Materia prima eliminada correctamente");
            // Actualizar la lista de materias primas asignadas
            this.obtenerMateriasPrimasAsignadas(this.proyectoSeleccionado.proyecto_id);
          } else {
            console.error("Error:", response.data.error);
          }
        } catch (error) {
          console.error("Error al eliminar materia prima:", error);
        }
      }
    },
    cerrarModalMateriaPrima() {
  this.mostrarModalMateriaPrima = false;
  this.materiaPrima = {
    proyecto_id: null,
    materia_prima_id: null,
    cantidad_utilizada: null,
    costo: null,
    fecha_utilizacion: new Date().toISOString().substr(0, 10)
  };
},

    async obtenerEmpleadosDisponibles() {
    try {
      const response = await axios.get("http://localhost/GestionConstructora/backend/empleados.php");
      if (Array.isArray(response.data)) {
        this.listaEmpleados = response.data;
      } else {
        console.error("Error: la respuesta no es un array.", response.data);
      }
    } catch (error) {
      console.error("Error al obtener empleados disponibles:", error);
    }
  },

  // Método para obtener empleados asignados a un proyecto
  async obtenerEmpleados(proyecto_id) {
  try {
    const response = await axios.get(`http://localhost/GestionConstructora/backend/proyectos_empleados.php?proyecto_id=${proyecto_id}`);
    if (Array.isArray(response.data)) {
      // Forzar la reactividad de Vue al actualizar `proyectoSeleccionado`
      this.proyectoSeleccionado = {
        ...this.proyectoSeleccionado,
        empleados: response.data
      };
    } else {
      console.error("Error: Se esperaba un array en response.data, pero se recibió:", response.data);
    }
  } catch (error) {
    console.error("Error al obtener empleados:", error);
  }
},



  abrirModalEmpleado(proyectoId) {
    this.empleado = {
      proyecto_id: proyectoId,
      empleado_id: null,
      fecha_asignacion: new Date().toISOString().substr(0, 10),
      fecha_liberacion: ""
    };
    this.obtenerEmpleadosDisponibles(); // Cargar empleados disponibles
    this.mostrarModalEmpleado = true;
  },

  async asignarEmpleado() {
  if (!this.empleado.proyecto_id || !this.empleado.empleado_id || !this.empleado.fecha_asignacion || !this.empleado.rol_en_proyecto) {
    alert("Por favor completa todos los campos requeridos para asignar un empleado.");
    return;
  }

  try {
    const response = await axios.post("http://localhost/GestionConstructora/backend/proyectos_empleados.php", this.empleado, {
      headers: { "Content-Type": "application/json" },
    });

    if (response.data.message) {
      console.log("Empleado asignado correctamente");

      // Actualizar la lista de empleados asignados al proyecto seleccionado
      await this.obtenerEmpleados(this.empleado.proyecto_id);
      // Refresca toda la lista de proyectos para ver la inversión actualizada
      await this.obtenerProyectos();

      // Cerrar el modal y resetear el formulario
      this.mostrarModalEmpleado = false;
      this.empleado = {
        proyecto_id: null,
        empleado_id: null,
        fecha_asignacion: new Date().toISOString().substr(0, 10),
        fecha_liberacion: "",
        rol_en_proyecto: ""
      };
    } else if (response.data.error) {
      alert("Error: " + response.data.error);
    }
  } catch (error) {
    console.error("Error al asignar empleado:", error);
  }
},



  async eliminarEmpleado(proyecto_empleado_id) {
    if (confirm("¿Estás seguro de que deseas eliminar este empleado del proyecto?")) {
      try {
        const response = await axios.post(
          "http://localhost/GestionConstructora/backend/proyectos_empleados.php",
          {
            proyecto_empleado_id: proyecto_empleado_id,
            _method: "DELETE"
          },
          {
            headers: { "Content-Type": "application/json" }
          }
        );

        if (response.data.error) {
          alert("Error: " + response.data.error);
        } else {
          // Actualiza la lista en el frontend
          this.proyectoSeleccionado.empleados = this.proyectoSeleccionado.empleados.filter(
            (item) => item.proyecto_empleado_id !== proyecto_empleado_id
          );
        }
      } catch (error) {
        console.error("Error al eliminar empleado:", error);
      }
    }
  },

    async obtenerMaquinariaDisponible() {
    try {
      const response = await axios.get("http://localhost/GestionConstructora/backend/maquinaria1.php");
      if (Array.isArray(response.data)) {
        this.listaMaquinaria = response.data;
      } else {
        console.error("Error: la respuesta no es un array.", response.data);
      }
    } catch (error) {
      console.error("Error al obtener maquinaria disponible:", error);
    }
  },


  // Método para obtener maquinaria asignada a un proyecto
  async obtenerMaquinaria(proyecto_id) {
  try {
    const response = await axios.get(`http://localhost/GestionConstructora/backend/proyectos_maquinaria.php?proyecto_id=${proyecto_id}`);
    console.log("Respuesta del servidor para maquinaria:", response.data); // Verifica los datos aquí
    if (Array.isArray(response.data)) {
      this.proyectoSeleccionado.maquinaria = response.data;
    } else {
      console.error("Error: la respuesta no es un array.", response.data);
    }
  } catch (error) {
    console.error("Error al obtener maquinaria:", error);
  }
},


  abrirModalMaquinaria(proyectoId) {
  console.log("Abriendo modal para proyecto ID:", proyectoId);
  this.maquinaria = {
    proyecto_id: proyectoId,
    maquinaria_id: null,
    fecha_asignacion: new Date().toISOString().substr(0, 10),
    fecha_liberacion: ""
  };
  this.obtenerMaquinariaDisponible(); // Cargar maquinaria disponible
  this.mostrarModalMaquinaria = true;
},

async asignarMaquinaria() {
  // Validación para asegurarse de que todos los campos requeridos están completos
  if (!this.maquinaria.proyecto_id || !this.maquinaria.maquinaria_id || !this.maquinaria.fecha_asignacion) {
    alert("Por favor completa todos los campos requeridos para asignar maquinaria.");
    return;
  }

  try {
    console.log("Asignando maquinaria:", this.maquinaria); // Verificación de datos antes de enviar
    const response = await axios.post("http://localhost/GestionConstructora/backend/proyectos_maquinaria.php", this.maquinaria, {
      headers: { "Content-Type": "application/json" },
    });
    
    console.log("Respuesta del servidor:", response.data);
    if (response.data.message) {
      console.log("Maquinaria asignada correctamente");
      this.obtenerMaquinaria(this.maquinaria.proyecto_id);
      this.mostrarModalMaquinaria = false;
    }
  } catch (error) {
    console.error("Error al asignar maquinaria:", error);
  }
},

async eliminarMaquinaria(proyecto_maquinaria_id) {
  if (confirm("¿Estás seguro de que deseas eliminar esta maquinaria?")) {
    try {
      const response = await axios.post(
        "http://localhost/GestionConstructora/backend/proyectos_maquinaria.php",
        {
          proyecto_maquinaria_id: proyecto_maquinaria_id,
          _method: "DELETE"
        },
        {
          headers: { "Content-Type": "application/json" }
        }
      );
      console.log("Respuesta de eliminación:", response.data);

      if (response.data.error) {
        alert("Error: " + response.data.error);
      } else {
        // Actualiza la lista en el frontend
        this.proyectoSeleccionado.maquinaria = this.proyectoSeleccionado.maquinaria.filter(
          (item) => item.proyecto_maquinaria_id !== proyecto_maquinaria_id
        );
      }
    } catch (error) {
      console.error("Error al eliminar maquinaria:", error);
    }
  }
},


    calcularPorcentajeAvance() {
    const totalTareas = this.proyectoSeleccionado.tareas.length;
    const tareasCompletadas = this.proyectoSeleccionado.tareas.filter(tarea => tarea.completada).length;
    
    if (totalTareas === 0) return 0; // Evitar división por cero
    return Math.round((tareasCompletadas / totalTareas) * 100);
  },
  async eliminarTarea(tareaId) {
  if (confirm("¿Estás seguro de que deseas eliminar esta tarea?")) {
    try {
      await axios.delete("http://localhost/GestionConstructora/backend/tareas.php", {
        data: { tarea_id: tareaId },
        headers: { "Content-Type": "application/json" },
      });

      // Eliminar la tarea del estado local
      this.proyectoSeleccionado.tareas = this.proyectoSeleccionado.tareas.filter(
        (tarea) => tarea.tarea_id !== tareaId
      );

      // Actualizar el porcentaje de avance inmediatamente
      this.actualizarPorcentajeAvance(this.proyectoSeleccionado.proyecto_id);

      console.log("Tarea eliminada y porcentaje de avance actualizado.");
    } catch (error) {
      console.error("Error al eliminar tarea:", error);
    }
  }
},

  
async crearTarea() {
  try {
    const response = await axios.post("http://localhost/GestionConstructora/backend/tareas.php", this.tarea, {
      headers: { "Content-Type": "application/json" },
    });

    if (response.data.message) {
      console.log("Tarea creada:", response.data.message);

      // Actualizar el porcentaje de avance en el frontend
      this.proyectoSeleccionado.porcentaje_avance = response.data.porcentaje_avance;

      // Recargar la lista de tareas del proyecto
      await this.obtenerTareas(this.proyectoSeleccionado.proyecto_id);

      // Refrescar la lista de proyectos en la página principal si es necesario
      this.obtenerProyectos();
      
      // Cerrar el modal de creación y resetear el formulario
      this.mostrarModalTarea = false;
      this.resetTarea();
    } else if (response.data.error) {
      console.error("Error en el backend:", response.data.error);
    }
  } catch (error) {
    console.error("Error al crear tarea:", error);
  }
},


  editarTarea(tarea) {
  this.tareaSeleccionada = {
    ...tarea,
    completada: !!tarea.completada // Asegura que completada sea booleano
  };
  this.mostrarModalEditarTarea = true;
},
async actualizarTarea() {
  try {
    // Realizar la solicitud para actualizar la tarea
    const response = await axios.post(
      "http://localhost/GestionConstructora/backend/tareas_proyecto.php",
      this.tareaSeleccionada
    );

    if (response.data.success) {
      // Actualizar la tarea en `proyectoSeleccionado.tareas`
      const index = this.proyectoSeleccionado.tareas.findIndex(
        (t) => t.tarea_id === this.tareaSeleccionada.tarea_id
      );
      if (index !== -1) {
        this.proyectoSeleccionado.tareas.splice(index, 1, {
          ...this.tareaSeleccionada,
        });
      }

      // Actualizar el porcentaje de avance en el frontend
      this.proyectoSeleccionado.porcentaje_avance = response.data.porcentaje_avance;

      // Recargar la lista de tareas del proyecto
      await this.obtenerTareas(this.proyectoSeleccionado.proyecto_id);

      // Refrescar la lista de proyectos en la página principal si es necesario
      this.obtenerProyectos();

      // Mostrar un mensaje de éxito
      console.log("Tarea actualizada y porcentaje de avance recalculado.");
    } else {
      console.error("Error al actualizar la tarea:", response.data.message);
    }
  } catch (error) {
    console.error("Error en la solicitud al servidor:", error);
  } finally {
    // Cerrar el modal de edición de tareas
    this.mostrarModalEditarTarea = false;
  }
},


async actualizarPorcentajeAvance(proyectoId) {
  try {
    const response = await axios.put(`http://localhost/GestionConstructora/backend/tareas_proyecto.php?update_percentage=true&proyecto_id=${proyectoId}`);
    console.log("Porcentaje de avance actualizado:", response.data.porcentaje_avance);

    // Actualizar el valor en `proyectoSeleccionado`
    if (this.proyectoSeleccionado.proyecto_id === proyectoId) {
      this.proyectoSeleccionado.porcentaje_avance = response.data.porcentaje_avance;
    }

    // Sincronizar con la lista de proyectos
    const index = this.proyectos.findIndex((p) => p.proyecto_id === proyectoId);
    if (index !== -1) {
      this.proyectos[index].porcentaje_avance = response.data.porcentaje_avance;
    }
  } catch (error) {
    console.error("Error al actualizar el porcentaje de avance:", error);
  }
},

  obtenerProyectos() {
    axios
      .get("http://localhost/GestionConstructora/backend/proyectos.php")
      .then((response) => {
        if (Array.isArray(response.data)) {
          this.proyectos = response.data;
        } else {
          console.error("Error: Se esperaba un array en response.data, pero se recibió:", response.data);
        }
      })
      .catch((error) => console.error("Error al obtener proyectos:", error));
  },

  obtenerTareas(proyecto_id) {
  axios
    .get(`http://localhost/GestionConstructora/backend/tareas_proyecto.php?proyecto_id=${proyecto_id}`)
    .then((response) => {
      console.log("Tareas recibidas:", response.data); // Verifica que las tareas se reciban correctamente
      if (Array.isArray(response.data)) {
        this.proyectoSeleccionado.tareas = response.data;
      } else {
        console.error("Error: Se esperaba un array en response.data, pero se recibió:", response.data);
      }
    })
    .catch((error) => console.error("Error al obtener tareas:", error));
},
    resetTarea() {
      this.tarea = {
        proyecto_id: null,
        nombre: "",
        descripcion: "",
        completada: false
      };
    },
    
    abrirModalTarea(proyectoId) {
      this.tarea = {
        proyecto_id: proyectoId,
        nombre: "",
        descripcion: "",
        completada: false
      };
      this.mostrarModalTarea = true;
    },
   
    abrirModalCrear() {
      this.resetProyecto();
      this.proyectoEdit = false;
      this.mostrarModal = true;
    },
    verDetalles(proyecto) {
  this.proyectoSeleccionado = { ...proyecto }; // Copia reactiva
  this.mostrarDetalles = true;

  // Cargar datos adicionales del proyecto seleccionado
  this.obtenerTareas(proyecto.proyecto_id);
  this.obtenerMaquinaria(proyecto.proyecto_id);
  this.obtenerEmpleados(proyecto.proyecto_id);
  this.obtenerMateriasPrimasAsignadas(proyecto.proyecto_id);
    },



    cerrarDetalles() {
      this.mostrarDetalles = false;
      this.proyectoSeleccionado = {};
    },
    onFileChange(event) {
      const file = event.target.files[0];
      const reader = new FileReader();
      reader.onload = e => {
        this.proyecto.imagen = e.target.result.split(",")[1]; // Base64 sin el prefijo
      };
      reader.readAsDataURL(file);
    },

    submitProyecto() {
      if (this.proyectoEdit) {
        this.actualizarProyecto();
      } else {
        this.crearProyecto();
      }
    },
    crearProyecto() {
      axios
        .post("http://localhost/GestionConstructora/backend/proyectos.php", this.proyecto)
        .then(response => {
          console.log("Proyecto creado:", response.data);
          this.obtenerProyectos();
          this.cancelarEdicion();
        })
        .catch(error => console.error("Error al crear proyecto:", error));
    },
    editarProyecto(proyecto) {
      this.proyecto = { ...proyecto, imagen: "" };
      this.proyectoEdit = true;
      this.mostrarModal = true;
    },
    actualizarProyecto() {
  const toast = useToast();
  const datosProyecto = { ...this.proyecto };
  datosProyecto.proyecto_id = this.proyecto.proyecto_id;

  if (!datosProyecto.imagen) delete datosProyecto.imagen;

  axios
    .put("http://localhost/GestionConstructora/backend/proyectos.php", datosProyecto)
    .then(response => {
      const data = response.data;

      if (data.error) {
        toast.error(`Error: ${data.error}`);
      } else {
        toast.success(data.message || "Proyecto actualizado correctamente.");
        
        // Verificar si la maquinaria fue liberada
        if (data.maquinaria_message) {
          toast.info(data.maquinaria_message); // "Maquinaria liberada."
        }

        this.obtenerProyectos();
        this.cancelarEdicion();
      }
    })
    .catch(error => {
      console.error("Error al actualizar proyecto:", error);
      toast.error("Ocurrió un error al actualizar el proyecto.");
    });
},


    eliminarProyecto(id) {
      if (confirm("¿Estás seguro de que deseas eliminar este proyecto?")) {
        axios
          .delete("http://localhost/GestionConstructora/backend/proyectos.php", { data: { proyecto_id: id } })
          .then(response => {
            console.log("Proyecto eliminado:", response.data);
            this.obtenerProyectos();
          })
          .catch(error => console.error("Error al eliminar proyecto:", error));
      }
    },
    cancelarEdicion() {
      this.resetProyecto();
      this.mostrarModal = false;
    },
    resetProyecto() {
      this.proyecto = {
        nombre: "",
        descripcion: "",
        ubicacion: "",
        fecha_inicio: "",
        fecha_fin: "",
        estado: "NI",
        porcentaje_avance: 0,
        inversion_inicial: 0,
        inversion_final: 0,
        imagen: ""
      };
    },
    formatCurrency(value) {
      return new Intl.NumberFormat("es-ES", { style: "currency", currency: "USD" }).format(value);
    },
    badgeClass(estado) {
      return estado === "NI"
        ? "bg-gray-200 text-gray-800"
        : estado === "EE"
        ? "bg-yellow-200 text-yellow-800"
        : "bg-green-200 text-green-800";
    }
  },
  created() {
    this.obtenerProyectos();
  },
  mounted() {
    this.obtenerMateriasPrimas();
  }
};
</script>
  <style scoped>
    .modal-content {
      max-height: calc(100vh - 100px); /* Ajusta el modal para que sea un poco más corto que la pantalla completa */
      overflow-y: auto;
    }
    .tasks-scroll {
      max-height: 12rem; /* Ajusta la altura máxima de la lista de tareas */
      overflow-y: auto;
    }
  </style>
