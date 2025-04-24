<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Recibe la lista de clientes del controlador ProjectController@create
const props = defineProps({
    clients: {
        type: Array,
        required: true,
    }
});

const form = useForm({
    name: '',
    client_id: '', // ID del cliente seleccionado
    status: 'pendiente', // Valor por defecto
    due_date: ''
});

const submit = () => {
    form.post(route('projects.store'), {
        onSuccess: () => form.reset('name', 'client_id', 'status', 'due_date'), // Resetear campos específicos
    });
};
</script>

<template>
    <Head title="Crear Proyecto" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Crear Nuevo Proyecto</h2>
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

                              <!-- Sección NUEVA con <select> -->
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
                                    <span v-if="form.processing">Guardando...</span>
                                    <span v-else>Guardar Proyecto</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>