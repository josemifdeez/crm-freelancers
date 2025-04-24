<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, watch } from 'vue';
import { PencilSquareIcon, TrashIcon, PlusIcon, ClipboardDocumentListIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import debounce from 'lodash.debounce';

// Props
const props = defineProps({
    tasks: { type: Object, required: true },
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
     router.get(route('tasks.index'),
        { search: search.value, status: statusFilter.value },
        { preserveState: true, preserveScroll: true, replace: true }
    );
};

const clearFilters = () => {
    search.value = '';
    statusFilter.value = '';
};

// Borrar Tarea
const deleteTask = (taskId) => {
    if (confirm('¿Estás seguro de eliminar esta tarea?')) {
        router.delete(route('tasks.destroy', taskId), { preserveScroll: true });
    }
};

// Clase de Badge
const getTaskStatusBadgeClass = (status) => {
    const normalizedStatus = String(status).toLowerCase().replace(/\s+/g, '-');
    const statusMap = { 'pendiente': 'task-status-badge--pendiente', 'en-curso': 'task-status-badge--en-curso', 'finalizada': 'task-status-badge--finalizada' };
    return `task-status-badge ${statusMap[normalizedStatus] || 'task-status-badge--default'}`;
};

</script>

<template>
    <Head title="Tareas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Tareas</h2>
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
                                    placeholder="Buscar por descripción..."
                                     class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pl-10 pr-4 py-2 text-sm"
                                />
                            </div>
                             <!-- Select de Estado -->
                            <div class="relative flex-grow w-full sm:w-auto sm:flex-grow-0"> 
                                 <select
                                    v-model="statusFilter"
                                     class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-2 text-sm">
                                     <!-- Texto Placeholder Actualizado -->
                                    <option value="">Estado (Todos)</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="en curso">En Curso</option>
                                    <option value="finalizada">Finalizada</option>
                                </select>
                            </div>
                            <!-- Botón Limpiar Filtros -->
                             <button v-if="search || statusFilter" @click="clearFilters" class="text-sm text-gray-500 hover:text-gray-700 underline whitespace-nowrap"> 
                                Limpiar
                             </button>
                        </div>

                        <!-- Botón Crear Tarea -->
                        <Link :href="route('tasks.create')"> <!-- O projects.create / tasks.create -->
                            <button type="button" class="button-primary-soft">
                            <PlusIcon class="h-5 w-5" /> <!-- Icono hereda color del texto -->
                            <span>Crear Tarea</span> <!-- O Crear Proyecto / Crear Tarea -->
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
                                     <th>Descripción</th>
                                     <th>Proyecto</th>
                                     <th>Estado</th>
                                     <th class="text-right"></th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <tr v-if="tasks.data.length === 0" class="empty-row">
                                      <td colspan="4">
                                          <div class="flex flex-col items-center justify-center">
                                              <ClipboardDocumentListIcon class="h-12 w-12 text-gray-400 mb-2" />
                                             <span v-if="filters.search || filters.status">No se encontraron tareas con los filtros aplicados.</span>
                                             <span v-else>No hay tareas pendientes ni completadas.</span>
                                         </div>
                                     </td>
                                 </tr>
                                 <tr v-for="task in tasks.data" :key="task.id">
                                     <td class="cell-primary">{{ task.description }}</td>
                                     <td class="cell-secondary">{{ task.project?.name ?? 'N/A' }}</td>
                                     <td>
                                         <span :class="getTaskStatusBadgeClass(task.status)">
                                             {{ task.status ?? 'N/A' }}
                                         </span>
                                     </td>
                                     <td class="actions-cell">
                                          <Link :href="route('tasks.edit', task.id)" class="action-button action-button--edit" title="Editar">
                                             <PencilSquareIcon class="h-5 w-5" />
                                         </Link>
                                         <button @click="deleteTask(task.id)" class="action-button action-button--delete" title="Eliminar">
                                             <TrashIcon class="h-5 w-5" />
                                         </button>
                                     </td>
                                 </tr>
                             </tbody>
                         </table>
                     </div>
                     <Pagination v-if="tasks.links.length > 3" :links="tasks.links" class="mt-6"/>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>