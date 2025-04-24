<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue'; // Para búsqueda
import { ref, watch } from 'vue';
import { PencilSquareIcon, TrashIcon, PlusIcon, FolderOpenIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import debounce from 'lodash.debounce';

// Props
const props = defineProps({
    projects: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

// Mensajes Flash
const page = usePage();
const successMessage = ref('');
watch(() => page.props.flash?.success, (newMessage) => {
  successMessage.value = newMessage;
  if (newMessage) { setTimeout(() => { successMessage.value = ''; }, 5000); }
}, { immediate: true });

// Estado para Filtros
const search = ref(props.filters.search);
const statusFilter = ref(props.filters.status ?? '');

// Watchers para Filtros
watch(search, debounce((value) => { applyFilters(); }, 300));
watch(statusFilter, (value) => { applyFilters(); });

const applyFilters = () => {
     router.get(route('projects.index'),
        { search: search.value, status: statusFilter.value },
        { preserveState: true, preserveScroll: true, replace: true }
    );
};

const clearFilters = () => {
    search.value = '';
    statusFilter.value = '';
};

// Borrar Proyecto
const deleteProject = (projectId) => {
    if (confirm('¿Estás seguro? Las tareas asociadas podrían eliminarse.')) {
        router.delete(route('projects.destroy', projectId), { preserveScroll: true });
    }
};

// Formatear Fecha
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    const date = new Date(dateString + 'T00:00:00');
    return date.toLocaleDateString('es-ES', options);
};

// Clase de Badge
const getStatusBadgeClass = (status) => {
    const normalizedStatus = String(status).toLowerCase().replace(/\s+/g, '-');
    const statusMap = { 'pendiente': 'status-badge--pendiente', 'en-curso': 'status-badge--en-curso', 'finalizado': 'status-badge--finalizado' };
    return `status-badge ${statusMap[normalizedStatus] || 'status-badge--default'}`;
};
</script>

<template>
    <Head title="Proyectos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Proyectos</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                 <div class="index-content-container space-y-6">

                    <!-- Filtros y Acciones -->
                    <div class="flex flex-col sm:flex-row flex-wrap justify-between items-center gap-4">
                        <!-- Grupo de Filtros -->
                        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto items-center"> 
                             <!-- Input de Búsqueda -->
                            <div class="relative flex-grow w-full sm:w-auto"> 
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                                </span>
                                <TextInput
                                    v-model="search"
                                    type="text"
                                    placeholder="Buscar por nombre..."
                                
                                    class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pl-10 pr-4 py-2 text-sm"
                                />
                            </div>
                             <!-- Select de Estado -->
                            <div class="relative flex-grow w-full sm:w-auto sm:flex-grow-0"> 
                                 <select
                                    v-model="statusFilter"
                                    
                                    class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-2 text-sm"
                                    >
                                    <!-- Texto Placeholder Actualizado -->
                                    <option value="">Estado (Todos)</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="en curso">En Curso</option>
                                    <option value="finalizado">Finalizado</option>
                                </select>
                            </div>
                            <!-- Botón Limpiar Filtros -->
                             <button v-if="search || statusFilter" @click="clearFilters" class="text-sm text-gray-500 hover:text-gray-700 underline whitespace-nowrap">
                                Limpiar
                             </button>
                        </div>

                        <!-- Botón Crear Proyecto -->
                        <Link :href="route('projects.create')"> 
                            <button type="button" class="button-primary-soft">
                            <PlusIcon class="h-5 w-5" /> 
                            <span>Crear Proyecto</span> 
                            </button>
                        </Link>
                    </div>


                    <div v-if="successMessage" class="flash-message-success">
                        {{ successMessage }}
                    </div>

                   
                     <div class="overflow-x-auto border border-gray-200 rounded-lg">
                         <table class="data-table">
                             <thead>
                                 <tr>
                                     <th>Nombre Proyecto</th>
                                     <th>Cliente</th>
                                     <th>Estado</th>
                                     <th>Fecha Entrega</th>
                                     <th></th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <tr v-if="projects.data.length === 0" class="empty-row">
                                      <td colspan="5">
                                         <div class="flex flex-col items-center justify-center">
                                              <FolderOpenIcon class="h-12 w-12 text-gray-400 mb-2" />
                                              <span v-if="filters.search || filters.status">No se encontraron proyectos con los filtros aplicados.</span>
                                              <span v-else>No has creado ningún proyecto todavía.</span>
                                         </div>
                                     </td>
                                 </tr>
                                 <tr v-for="project in projects.data" :key="project.id">
                                     <td class="cell-primary">{{ project.name }}</td>
                                     <td class="cell-secondary">{{ project.client?.name ?? 'N/A' }}</td>
                                     <td>
                                         <span :class="getStatusBadgeClass(project.status)">
                                             {{ project.status ?? 'N/A' }}
                                         </span>
                                     </td>
                                     <td class="cell-secondary">{{ formatDate(project.due_date) }}</td>
                                     <td class="actions-cell">
                                          <Link :href="route('projects.edit', project.id)" class="action-button action-button--edit" title="Editar">
                                             <PencilSquareIcon class="h-5 w-5" />
                                         </Link>
                                         <button @click="deleteProject(project.id)" class="action-button action-button--delete" title="Eliminar">
                                             <TrashIcon class="h-5 w-5" />
                                         </button>
                                     </td>
                                 </tr>
                             </tbody>
                         </table>
                     </div>
                     <Pagination v-if="projects.links.length > 3" :links="projects.links" class="mt-6"/>


                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>