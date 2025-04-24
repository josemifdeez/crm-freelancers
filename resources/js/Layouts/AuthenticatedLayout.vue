// resources/js/Layouts/AuthenticatedLayout.vue
<script setup>
import { ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3'; // <-- Importa usePage
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
// Importar iconos para el dropdown
import { Cog6ToothIcon, ArrowLeftStartOnRectangleIcon } from '@heroicons/vue/24/outline';

const showingNavigationDropdown = ref(false);
const page = usePage(); // <-- Obtenemos acceso a $page

// Función para cerrar sesión
const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <!-- Barra de navegación principal -->
            <nav class="main-navigation bg-white border-b border-gray-100"> 
                <!-- Contenedor interno -->
                <div class="nav-container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> 
                    <!-- Contenido flex -->
                    <div class="nav-content flex justify-between h-16"> 
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <!-- ¡CAMBIO! El logo ahora apunta a la landing -->
                                <Link href="/">
                                    <img
                                    src="/img/logo-crm.webp" 
                                    alt="Your Name"                 
                                    class="h-7 w-auto"
                                    loading="lazy"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links (Desktop) -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <!-- ***** INICIO LÓGICA CONDICIONAL ***** -->

                                <!-- ENLACES PARA USUARIOS AUTENTICADOS -->
                                <template v-if="page.props.auth.user">
                                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                        Panel de usuario
                                    </NavLink>
                                    <NavLink :href="route('clients.index')" :active="route().current('clients.*')"> 
                                        Clientes
                                    </NavLink>
                                    <NavLink :href="route('projects.index')" :active="route().current('projects.*')">
                                        Proyectos
                                    </NavLink>
                                    <NavLink :href="route('tasks.index')" :active="route().current('tasks.*')">
                                        Tareas
                                    </NavLink>
                                </template>

                                <!-- ENLACES PARA INVITADOS (NO AUTENTICADOS) -->
                                <template v-else>
                                    <NavLink v-if="page.props.canLogin" :href="route('login')" :active="route().current('login')">
                                        Iniciar Sesión
                                    </NavLink>
                                    <NavLink v-if="page.props.canRegister" :href="route('register')" :active="route().current('register')">
                                        Registrarse
                                    </NavLink>
                                </template>
                                <!-- ***** FIN LÓGICA CONDICIONAL ***** -->
                            </div>
                        </div>

                         <!-- Dropdown de Usuario (SÓLO para AUTENTICADOS) -->
                        <div v-if="page.props.auth.user" class="hidden sm:flex sm:items-center sm:ml-6">
                            <div class="ml-3 relative user-dropdown">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="dropdown-trigger-button inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                {{ page.props.auth.user.name }}
                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')" class="flex items-center">
                                             <Cog6ToothIcon class="mr-2 h-5 w-5 text-gray-400" />
                                             <span>Perfil</span>
                                        </DropdownLink>
                                        <!-- Usamos @click para llamar a la función logout -->
                                        <DropdownLink @click="logout" as="button" class="flex items-center w-full text-left">
                                            <ArrowLeftStartOnRectangleIcon class="mr-2 h-5 w-5 text-gray-400" />
                                            <span>Cerrar sesión</span>
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                             <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu (Mobile) -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden responsive-nav-menu">
                    <div class="responsive-links-container pt-2 pb-3 space-y-1"> 
                        <!-- ***** INICIO LÓGICA CONDICIONAL RESPONSIVE ***** -->

                        <!-- ENLACES RESPONSIVE AUTENTICADOS -->
                        <template v-if="page.props.auth.user">
                            <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Dashboard
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('clients.index')" :active="route().current('clients.*')">
                                Clientes
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('projects.index')" :active="route().current('projects.*')">
                                Proyectos
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('tasks.index')" :active="route().current('tasks.*')">
                                Tareas
                            </ResponsiveNavLink>
                        </template>

                        <!-- ENLACES RESPONSIVE INVITADOS -->
                         <template v-else>
                            <ResponsiveNavLink v-if="page.props.canLogin" :href="route('login')" :active="route().current('login')">
                                Iniciar Sesión
                            </ResponsiveNavLink>
                            <ResponsiveNavLink v-if="page.props.canRegister" :href="route('register')" :active="route().current('register')">
                                Registrarse
                            </ResponsiveNavLink>
                        </template>
                        <!-- ***** FIN LÓGICA CONDICIONAL RESPONSIVE ***** -->
                    </div>

                    <!-- Responsive Settings Options (SÓLO para AUTENTICADOS) -->
                    <div v-if="page.props.auth.user" class="responsive-settings pt-4 pb-1 border-t border-gray-200"> 
                        <div class="user-info px-4"> 
                            <div class="font-medium text-base text-gray-800">
                                {{ page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ page.props.auth.user.email }}</div>
                        </div>

                        <div class="settings-links mt-3 space-y-1"> 
                             <ResponsiveNavLink :href="route('profile.edit')">
                                Perfil
                            </ResponsiveNavLink>
                            <!-- Usamos @click para llamar a la función logout -->
                            <ResponsiveNavLink @click="logout" as="button">
                                Cerrar sesión
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="page-header bg-white shadow" v-if="$slots.header"> 
                <div class="header-content max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main> 
                <slot />
            </main>
        </div>
    </div>
</template>