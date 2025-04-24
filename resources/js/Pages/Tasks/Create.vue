<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import TextInput from '@/Components/TextInput.vue'; // Para el estado
import { Head, Link, useForm } from '@inertiajs/vue3';

// Recibe la lista de proyectos del usuario y opcionalmente un ID preseleccionado
const props = defineProps({
    projects: {
        type: Array,
        required: true,
    },
    selectedProjectId: {
        type: [String, Number],
        default: '',
    }
});

const form = useForm({
    description: '',
    project_id: props.selectedProjectId, // Usa el ID preseleccionado si existe
    status: 'todo' // Valor por defecto
});

const submit = () => {
    form.post(route('tasks.store'), {
        onSuccess: () => form.reset('description', 'project_id', 'status'),
    });
};
</script>

<template>
    <Head title="Crear Tarea" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Crear Nueva Tarea</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">

                            <div class="mt-4">
                                <InputLabel for="project_id" value="Proyecto Asociado *" />
                                <select
                                    id="project_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    v-model="form.project_id"
                                    required
                                >
                                    <option value="" disabled>-- Selecciona un Proyecto --</option>
                                    <option v-for="project in projects" :key="project.id" :value="project.id">
                                        {{ project.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.project_id" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="description" value="Descripción *" />
                                <TextAreaInput
                                    id="description"
                                    class="mt-1 block w-full"
                                    v-model="form.description"
                                    required
                                    rows="4"
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="status" value="Estado *" />
                                <select
                                id="status"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                v-model="form.status"
                                required
                                >
                                <option value="pendiente">Pendiente</option>
                                <option value="en curso">En Curso</option>
                                <option value="finalizada">Finalizada</option>
                                <!-- Añade más opciones si las necesitas -->
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>


                            <div class="flex items-center justify-end mt-6">
                                <Link :href="route('tasks.index')" class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                                    Cancelar
                                </Link>

                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    <span v-if="form.processing">Guardando...</span>
                                    <span v-else>Guardar Tarea</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>