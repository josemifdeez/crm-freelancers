<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Recibe el proyecto a editar y la lista de clientes
const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
    clients: {
        type: Array,
        required: true,
    }
});

const form = useForm({
    name: props.project.name,
    client_id: props.project.client_id, // El ID ya viene asignado
    status: props.project.status ?? 'pendiente',
    due_date: props.project.due_date ?? ''
});

const submit = () => {
    form.put(route('projects.update', props.project.id));
};
</script>

<template>
    <Head :title="'Editar Proyecto - ' + project.name" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Proyecto: {{ project.name }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">

                             <div>
                                <InputLabel for="name" value="Nombre del Proyecto *" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="client_id" value="Cliente Asociado *" />
                                <select
                                    id="client_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    v-model="form.client_id"
                                    required
                                >
                                    <option value="" disabled>-- Selecciona un Cliente --</option>
                                    <option v-for="client in clients" :key="client.id" :value="client.id">
                                        {{ client.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.client_id" />
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
                                <option value="finalizado">Finalizado</option>
                                <!-- Añade más opciones si las necesitas -->
                                </select>
                                <InputError class="mt-2" :message="form.errors.status" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="due_date" value="Fecha de Entrega" />
                                <TextInput
                                    id="due_date"
                                    type="date"
                                    class="mt-1 block w-full"
                                    v-model="form.due_date"
                                />
                                <InputError class="mt-2" :message="form.errors.due_date" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <Link :href="route('projects.index')" class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                                    Cancelar
                                </Link>

                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                     <span v-if="form.processing">Actualizando...</span>
                                     <span v-else>Actualizar Proyecto</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>