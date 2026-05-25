<template>
    <div class="p-6">
        <h2 class="text-2xl font-semibold mb-4">Gestión de Usuarios</h2>

        <!-- Botón para abrir el modal de creación -->
        <button @click="abrirModalCrear" style="background-color: #2c3a4e;" class="text-white font-semibold px-4 py-2 rounded-md mb-4">
            Agregar Usuario
        </button>

        <!-- Modal para Crear/Editar Usuario -->
        <div v-if="mostrarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg w-full max-w-md">
                <h3 class="text-xl font-semibold mb-4">{{ modoEdicion ? "Editar Usuario" : "Nuevo Usuario" }}</h3>
                <form @submit.prevent="submitForm">
                    <label class="block mb-2">Nombre completo</label>
                    <input v-model="usuario.nombre" type="text" class="w-full mb-4 border rounded p-2" required />

                    <label class="block mb-2">Nombre de usuario</label>
                    <input v-model="usuario.username" type="text" class="w-full mb-4 border rounded p-2" required />

                    <label class="block mb-2">Email</label>
                    <input v-model="usuario.email" type="email" class="w-full mb-4 border rounded p-2" required />

                    <label class="block mb-2">Contraseña</label>
                    <input v-model="usuario.password" type="password" class="w-full mb-4 border rounded p-2"
                        :disabled="modoEdicion" />

                    <label class="block mb-2">Rol</label>
                    <select v-model="usuario.rol_id" class="w-full mb-4 border rounded p-2" required>
                        <option value="" disabled>Seleccione un rol</option> <!-- Placeholder -->
                        <option v-for="rol in roles" :key="rol.rol_id" :value="rol.rol_id">{{ rol.nombre }}</option>
                    </select>

                    <div class="flex justify-end">
                        <button type="button" @click="cerrarModal"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded mr-2">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">{{ modoEdicion ?
                            "Actualizar Usuario" : "Crear Usuario" }}</button>
                    </div>
                </form>
            </div>
        </div>

        <h3 class="text-lg font-semibold mt-6">Lista de Usuarios</h3>
        <input type="text" v-model="searchQuery" placeholder="Buscar usuario..."
            class="w-full mt-2 mb-4 px-4 py-2 border border-gray-300 rounded-md" />

        <!-- Tabla de Usuarios -->
        <div class="overflow-x-auto">
            <div class="overflow-x-auto overflow-y-auto" style="max-height: 400px;">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre Completo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in filteredUsers" :key="user.usuario_id"
                        class="hover:bg-gray-100 transition duration-150">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ user.nombre }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ user.username }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ user.email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ user.rol }}</td>
                        <td class="px-6 py-4 text-sm font-medium space-x-2">
                            <button @click="cargarUsuario(user)"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-semibold py-1 px-2 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                Editar
                            </button>
                            <button @click="eliminarUsuario(user.usuario_id)"
                            class="bg-red-500 hover:bg-red-700 text-white font-semibold py-1 px-2 rounded-lg shadow-md transition duration-200 ease-in-out transform hover:scale-105">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            usuarios: [],
            roles: [], // Arreglo para almacenar roles
            usuario: { nombre: "", username: "", email: "", password: "", rol_id: null },
            modoEdicion: false,
            mostrarModal: false,
            searchQuery: "",
            mensaje: "",
        };
    },
    computed: {
        filteredUsers() {
            return this.usuarios.filter((user) => {
                return (
                    user.nombre.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    user.username.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    user.email.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            });
        },
    },
    methods: {
        obtenerUsuarios() {
            axios.get("http://localhost/GestionConstructora/backend/usuarios.php")
                .then((response) => {
                    this.usuarios = response.data;
                    this.mostrarMensaje("Usuarios cargados correctamente.");
                })
                .catch((error) => {
                    console.error("Error al obtener usuarios:", error);
                    this.mostrarMensaje("Error al obtener usuarios.");
                });
        },
        obtenerRoles() {
            axios.get("http://localhost/GestionConstructora/backend/roles.php")
                .then((response) => {
                    console.log("Roles cargados:", response.data); // Verificar en la consola
                    this.roles = response.data;
                })
                .catch((error) => {
                    console.error("Error al obtener roles:", error);
                });
        },
        abrirModalCrear() {
            this.resetFormulario();
            this.mostrarModal = true;
            this.modoEdicion = false;
        },
        cerrarModal() {
            this.mostrarModal = false;
            this.resetFormulario();
        },
        submitForm() {
            if (!this.validarUsuario()) {
                return; // Si la validación falla, no continuar
            }
            if (this.modoEdicion) {
                this.actualizarUsuario();
            } else {
                this.crearUsuario();
            }
        },
        crearUsuario() {
            axios.post("http://localhost/GestionConstructora/backend/usuarios.php", this.usuario)
                .then((response) => {
                    if (response.data.message) {
                        this.mostrarMensaje(response.data.message);
                        this.obtenerUsuarios(); // Volver a cargar la lista de usuarios
                        this.cerrarModal();
                    } else if (response.data.error) {
                        this.mostrarMensaje(response.data.error);
                    }
                })
                .catch((error) => {
                    console.error("Error al crear usuario:", error);
                    this.mostrarMensaje("Error al crear usuario.");
                });
        },
        cargarUsuario(user) {
            this.usuario = { ...user, password: "" }; // Limpia la contraseña para no mostrarla
            this.usuario.rol_id = user.rol_id; // Asegúrate de que rol_id esté presente
            this.modoEdicion = true;
            this.mostrarModal = true;
        },
        actualizarUsuario() {
            const datosUsuario = { ...this.usuario };
            if (!datosUsuario.password) {
                delete datosUsuario.password;
            }
            axios.put("http://localhost/GestionConstructora/backend/usuarios.php", datosUsuario)
                .then((response) => {
                    this.mostrarMensaje(response.data.message || "Usuario actualizado correctamente.");
                    this.obtenerUsuarios();
                    this.cerrarModal();
                })
                .catch((error) => {
                    console.error("Error al actualizar usuario:", error);
                    this.mostrarMensaje("Error al actualizar usuario.");
                });
        },
        eliminarUsuario(id) {
            if (confirm("¿Estás seguro de que deseas eliminar este usuario?")) {
                axios.delete("http://localhost/GestionConstructora/backend/usuarios.php", { data: { usuario_id: id } })
                    .then((response) => {
                        this.mostrarMensaje(response.data.message || "Usuario eliminado correctamente.");
                        this.obtenerUsuarios();
                    })
                    .catch((error) => {
                        console.error("Error al eliminar usuario:", error);
                        this.mostrarMensaje("Error al eliminar usuario.");
                    });
            }
        },
        mostrarMensaje(texto) {
            this.mensaje = texto;
            setTimeout(() => (this.mensaje = ""), 3000);
        },
        resetFormulario() {
            this.usuario = { nombre: "", username: "", email: "", password: "", rol_id: null };
            this.modoEdicion = false;
        },
        validarUsuario() {
            if (
                !this.usuario.nombre ||
                !this.usuario.username ||
                !this.usuario.email ||
                (!this.modoEdicion && !this.usuario.password) || // No se requiere contraseña al editar
                !this.usuario.rol_id
            ) {
                this.mostrarMensaje("Todos los campos son obligatorios.");
                return false;
            }

            if (!/[a-zA-Z]/.test(this.usuario.username) || !isNaN(this.usuario.username)) {
                this.mostrarMensaje("El username debe contener letras y no puede ser solo números.");
                return false;
            }

            if (!this.modoEdicion && this.usuario.password.length < 8) {
                this.mostrarMensaje("La contraseña debe tener al menos 8 caracteres.");
                return false;
            }

            return true;
        },
    },
    created() {
        this.obtenerUsuarios();
        this.obtenerRoles(); // Cargar roles al iniciar
    },
};
</script>