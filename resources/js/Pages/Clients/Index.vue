<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, watch } from 'vue';
import { PencilSquareIcon, TrashIcon, PlusIcon, UserGroupIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import debounce from 'lodash.debounce';

const props = defineProps({
    clients: { type: Object, required: true },
    filters: { type: Object, default: () => ({ search: '', company: '' }) },
    companiesList: { type: Array, default: () => [] },
});

const page = usePage();
const successMessage = ref('');
watch(() => page.props.flash?.success, (newMessage) => {
  successMessage.value = newMessage;
  if (newMessage) { setTimeout(() => { successMessage.value = ''; }, 5000); }
}, { immediate: true });

const search = ref(props.filters.search ?? '');
const companyFilter = ref(props.filters.company ?? '');

watch(search, debounce((value) => { applyFilters(); }, 300));
watch(companyFilter, () => { applyFilters(); });

const applyFilters = () => {
    router.get(route('clients.index'),
        {
            search: search.value,
            company: companyFilter.value,
        },
        { preserveState: true, preserveScroll: true, replace: true }
    );
};

const clearFilters = () => {
    search.value = '';
    companyFilter.value = '';
};

const deleteClient = (clientId) => {
    if (confirm('¿Estás seguro? Esta acción no se puede deshacer.')) {
        router.delete(route('clients.destroy', clientId), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Clientes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Clientes</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="index-content-container space-y-6">

                    <div class="flex flex-col sm:flex-row flex-wrap justify-between items-center gap-4">

                        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto items-center">
                            <div class="relative flex-grow w-full sm:w-auto">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                                </span>
                                <TextInput
                                    v-model="search"
                                    type="text"
                                    placeholder="Buscar cliente..."
                                    class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pl-10 pr-4 py-2 text-sm"
                                />
                                <button
                                    v-if="search"
                                    @click="search = ''"
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                                    title="Limpiar búsqueda">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="relative flex-grow w-full sm:w-auto sm:flex-grow-0">
                                <select
                                    v-model="companyFilter"
                                    class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm py-2 text-sm pr-10 appearance-none"
                                    aria-label="Filtrar por empresa"
                                >
                                    <option value="">Empresa (Todas)</option>
                                    <option v-for="company in companiesList" :key="company" :value="company">
                                        {{ company }}
                                    </option>
                                </select>
                            </div>

                            <button
                                v-if="search || companyFilter"
                                @click="clearFilters"
                                class="text-sm text-gray-500 hover:text-gray-700 underline whitespace-nowrap">
                                Limpiar 
                             </button>
                        </div>

                        <div class="flex-shrink-0">
                            <Link :href="route('clients.create')">
                                <button type="button" class="button-primary-soft">
                                    <PlusIcon class="h-5 w-5" />
                                    <span>Crear Cliente</span>
                                </button>
                            </Link>
                        </div>
                    </div>


                    <div v-if="successMessage" class="flash-message-success">
                        {{ successMessage }}
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Empresa</th>
                                    <th class="text-right"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="clients.data.length === 0" class="empty-row">
                                    <td colspan="5">
                                        <div class="flex flex-col items-center justify-center">
                                             <UserGroupIcon class="h-12 w-12 text-gray-400 mb-2" />
                                            <span v-if="search || companyFilter">No se encontraron clientes con los filtros aplicados.</span>
                                            <span v-else>Aún no has añadido ningún cliente.</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-for="client in clients.data" :key="client.id">
                                    <td class="cell-primary">{{ client.name }}</td>
                                    <td class="cell-secondary">{{ client.email ?? '-' }}</td>
                                    <td class="cell-secondary">{{ client.phone ?? '-' }}</td>
                                    <td class="cell-secondary">{{ client.company ?? '-' }}</td>
                                    <td class="actions-cell">
                                        <Link :href="route('clients.edit', client.id)" class="action-button action-button--edit" title="Editar">
                                            <PencilSquareIcon class="h-5 w-5" />
                                        </Link>
                                        <button @click="deleteClient(client.id)" class="action-button action-button--delete" title="Eliminar">
                                            <TrashIcon class="h-5 w-5" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <Pagination v-if="clients.links.length > 3" :links="clients.links" class="mt-6"/>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>