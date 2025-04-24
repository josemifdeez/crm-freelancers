<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    client: {
        type: Object,
        required: true,
    }
});

const form = useForm({
    name: props.client.name,
    email: props.client.email ?? '',
    phone: props.client.phone ?? '',
    company: props.client.company ?? '',
    notes: props.client.notes ?? ''
});

const submit = () => {
    form.put(route('clients.update', props.client.id), {
        // preserveScroll: true, // Opcional si lo necesitas
    });
};

</script>

<template>
    <Head :title="'Editar Cliente - ' + client.name" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Cliente: {{ client.name }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="name" value="Nombre *" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    autocomplete="email"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="phone" value="Teléfono" />
                                <TextInput
                                    id="phone"
                                    type="tel"
                                    class="mt-1 block w-full"
                                    v-model="form.phone"
                                    autocomplete="tel"
                                />
                                <InputError class="mt-2" :message="form.errors.phone" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="company" value="Empresa" />
                                <TextInput
                                    id="company"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.company"
                                    autocomplete="organization"
                                />
                                <InputError class="mt-2" :message="form.errors.company" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="notes" value="Notas" />
                                <TextAreaInput
                                    id="notes"
                                    class="mt-1 block w-full"
                                    v-model="form.notes"
                                    rows="4"
                                />
                                <InputError class="mt-2" :message="form.errors.notes" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <Link :href="route('clients.index')" class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                                    Cancelar
                                </Link>

                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                     <span v-if="form.processing">Actualizando...</span>
                                     <span v-else>Actualizar Cliente</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>