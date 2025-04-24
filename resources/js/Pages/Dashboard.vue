<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3'; // Asegúrate de importar router y Link
import { ref, computed } from 'vue'; // Asegúrate de importar ref
// Importar iconos necesarios
import {
    UserGroupIcon, BriefcaseIcon, ClipboardDocumentCheckIcon, PlusIcon, // PlusIcon para los botones de añadir
    CalendarDaysIcon, ListBulletIcon, PencilSquareIcon, FolderIcon, CheckCircleIcon
} from '@heroicons/vue/24/outline';

// --- Props que vienen del DashboardController ---
const props = defineProps({
    clientCount: { type: Number, default: 0 },
    ongoingProjectCount: { type: Number, default: 0 },
    pendingTaskCount: { type: Number, default: 0 },
    upcomingProjects: { type: Array, default: () => [] },
    projectsWithPendingTasks: { type: Array, default: () => [] },
});

// --- Estado Local ---
const completingTaskId = ref(null);

// --- Funciones Helper ---
// Formatear fecha para la vista de calendario
const formatCalendarDate = (dateString) => {
    if (!dateString) return { day: '-', month: '-' };
    const date = new Date(dateString + 'T00:00:00');
    return {
        day: date.toLocaleDateString('es-ES', { day: 'numeric' }),
        month: date.toLocaleDateString('es-ES', { month: 'short' }).replace('.', '').toUpperCase(),
    };
};

// Formatear fecha estándar
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    const date = new Date(dateString + 'T00:00:00');
    return date.toLocaleDateString('es-ES', options);
};

// --- Acciones ---
// Marcar tarea como completada
const completeTask = (taskId) => {
    if (completingTaskId.value) return;
    completingTaskId.value = taskId;

    router.patch(route('tasks.complete', taskId), {}, {
        preserveScroll: true,
        onFinish: () => {
            completingTaskId.value = null;
        }
    });
};

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Panel Principal
            </h2>
        </template>

        <div class="py-12">
            <!-- Grid principal del Dashboard -->
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- === Columna Izquierda: Solo Estadísticas (con botón) === -->
                <div class="lg:col-span-1 space-y-6">

                    <!-- Sección de Estadísticas (ACTUALIZADA con botón +) -->
                    <div class="stat-card">
                         <!-- Agrupar icono y texto -->
                         <div class="stat-card__main-content">
                            <div class="stat-card__icon-wrapper stat-card__icon-wrapper--clients">
                                <UserGroupIcon class="h-6 w-6" />
                            </div>
                            <div class="stat-card__content">
                                <span class="stat-card__value">{{ clientCount }}</span>
                                <span class="stat-card__label">Clientes Activos</span>
                            </div>
                         </div>
                         <!-- Botón Añadir Cliente -->
                         <Link :href="route('clients.create')" class="stat-card__add-button stat-card__add-button--clients" title="Nuevo Cliente">
                             <PlusIcon class="h-5 w-5" />
                         </Link>
                     </div>

                     <div class="stat-card">
                          <!-- Agrupar icono y texto -->
                         <div class="stat-card__main-content">
                            <div class="stat-card__icon-wrapper stat-card__icon-wrapper--projects">
                                <BriefcaseIcon class="h-6 w-6" />
                            </div>
                            <div class="stat-card__content">
                                <span class="stat-card__value">{{ ongoingProjectCount }}</span>
                                <span class="stat-card__label">Proyectos en Curso</span>
                            </div>
                         </div>
                          <!-- Botón Añadir Proyecto -->
                         <Link :href="route('projects.create')" class="stat-card__add-button stat-card__add-button--projects" title="Nuevo Proyecto">
                             <PlusIcon class="h-5 w-5" />
                         </Link>
                     </div>

                     <div class="stat-card">
                         <!-- Agrupar icono y texto -->
                          <div class="stat-card__main-content">
                            <div class="stat-card__icon-wrapper stat-card__icon-wrapper--tasks">
                                <ClipboardDocumentCheckIcon class="h-6 w-6" />
                            </div>
                            <div class="stat-card__content">
                                <span class="stat-card__value">{{ pendingTaskCount }}</span>
                                <span class="stat-card__label">Tareas Pendientes</span>
                            </div>
                          </div>
                           <!-- Botón Añadir Tarea -->
                          <Link :href="route('tasks.create')" class="stat-card__add-button stat-card__add-button--tasks" title="Nueva Tarea">
                              <PlusIcon class="h-5 w-5" />
                          </Link>
                     </div>
                    <!-- Fin Sección Estadísticas -->

                    <!-- Panel de Acciones Rápidas ELIMINADO -->

                </div> <!-- === Fin Columna Izquierda === -->


                <!-- === Columna Derecha: Listas Dinámicas === -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Sección Próximas Entregas -->
                    <div class="info-panel">
                        <h3 class="info-panel__title">
                            <CalendarDaysIcon class="title-icon" />
                            Próximas Entregas (14 días)
                        </h3>
                        <div v-if="upcomingProjects.length > 0">
                            <ul class="info-panel__list">
                                <li v-for="project in upcomingProjects" :key="'upcoming-'+project.id" class="calendar-item">
                                    <div class="calendar-item__date">
                                        <span class="day">{{ formatCalendarDate(project.due_date).day }}</span>
                                        <span class="month">{{ formatCalendarDate(project.due_date).month }}</span>
                                    </div>
                                    <div class="calendar-item__details">
                                        <span class="project-name">{{ project.name }}</span>
                                        <span class="client-name">{{ project.client?.name ?? 'Sin cliente' }}</span>
                                    </div>
                                    <div class="calendar-item__action">
                                        <Link :href="route('projects.edit', project.id)" class="action-button action-button--edit" title="Editar Proyecto">
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </Link>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div v-else class="info-panel__empty-state">
                            <CalendarDaysIcon class="h-8 w-8 mx-auto text-gray-400 mb-2" />
                            No hay entregas próximas.
                        </div>
                    </div>

                    <!-- Sección Tareas Pendientes -->
                    <div class="info-panel">
                        <h3 class="info-panel__title">
                             <ListBulletIcon class="title-icon" />
                            Tareas Pendientes
                        </h3>
                         <div v-if="projectsWithPendingTasks.length > 0">
                             <div v-for="project in projectsWithPendingTasks" :key="'proj-group-'+project.id" class="project-group">
                                 <h4 class="project-group__header">
                                      <FolderIcon class="header-icon" />
                                     {{ project.name }}
                                 </h4>
                                 <ul class="project-group__task-list">
                                     <li v-for="task in project.tasks" :key="'task-'+task.id" class="project-group__task-item">
                                         <span class="task-description">{{ task.description }}</span>
                                         <button
                                             @click="completeTask(task.id)"
                                             class="complete-button"
                                             :disabled="completingTaskId === task.id"
                                             title="Marcar como completada">
                                              <!-- Icono de carga o completado -->
                                              <svg v-if="completingTaskId === task.id" class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                             <CheckCircleIcon v-else class="h-5 w-5" />
                                         </button>
                                     </li>
                                 </ul>
                             </div>
                         </div>
                         <div v-else class="info-panel__empty-state">
                             <ClipboardDocumentCheckIcon class="h-8 w-8 mx-auto text-gray-400 mb-2" />
                             ¡Sin tareas pendientes!
                         </div>
                     </div>

                </div> <!-- === Fin Columna Derecha === -->

            </div> <!-- === Fin Grid Principal === -->
        </div>
    </AuthenticatedLayout>
</template>