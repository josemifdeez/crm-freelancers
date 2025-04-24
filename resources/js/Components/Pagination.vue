<template>
    <!-- Solo muestra el contenedor si hay enlaces de paginación -->
    <div v-if="links.length > 3" class="flex justify-center mt-4 space-x-1">
      <!-- Itera sobre cada enlace proporcionado por Laravel Paginator -->
      <template v-for="(link, k) in links" :key="k">
        <!-- Si el enlace es una URL nula (ej. '...', puntos suspensivos), muéstralo como texto -->
        <div
          v-if="link.url === null"
          class="px-4 py-2 text-sm leading-4 text-gray-400 border rounded cursor-default"
          v-html="link.label"
        />
        <!-- Si el enlace es la página activa, muéstralo con estilo diferente -->
        <Link
          v-else-if="link.active"
          class="px-4 py-2 text-sm font-bold leading-4 text-white bg-indigo-600 border border-indigo-600 rounded hover:bg-indigo-700 focus:border-indigo-700 focus:text-white"
          :href="link.url"
          v-html="link.label"
        />
        <!-- Si es un enlace normal a otra página -->
        <Link
          v-else
          class="px-4 py-2 text-sm leading-4 bg-white border rounded text-gray-700 hover:bg-gray-100 focus:border-indigo-300 focus:text-gray-700"
          :href="link.url"
          v-html="link.label"
        />
      </template>
    </div>
  </template>
  
  <script setup>
  import { Link } from '@inertiajs/vue3';
  
  // Define las props que este componente espera recibir
  // 'links' será el array de enlaces que viene de la paginación de Laravel (`clients.links`)
  defineProps({
    links: Array,
  });
  </script>