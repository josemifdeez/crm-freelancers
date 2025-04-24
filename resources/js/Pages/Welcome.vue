<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
// ApplicationLogo ya no se importa aquí porque fue reemplazado en el header
// y el tech stack usa LaravelLogoRed.
import {
    UserGroupIcon, BriefcaseIcon, ClipboardDocumentCheckIcon,
    ArrowRightIcon
} from '@heroicons/vue/24/outline';

// Importa los componentes de logos SVG
import LaravelLogoRed from '@/Components/Logos/LaravelLogoRed.vue'; // Logo rojo específico para Tech Stack
import VueLogo from '@/Components/Logos/VueLogo.vue';
import TailwindLogo from '@/Components/Logos/TailwindLogo.vue';
import SassLogo from '@/Components/Logos/SassLogo.vue';
import SQLogo from '@/Components/Logos/SQLogo.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const page = usePage();

// Array de características
const features = [
    { name: 'Gestión de Clientes', description: 'Mantén toda la información de contacto, notas y proyectos asociados a cada cliente en un solo lugar.', icon: UserGroupIcon, colorClasses: 'bg-blue-100 text-blue-600' },
    { name: 'Seguimiento de Proyectos', description: 'Visualiza el estado (pendiente, en curso, finalizado) y no olvides ninguna fecha de entrega importante.', icon: BriefcaseIcon, colorClasses: 'bg-indigo-100 text-indigo-600' },
    { name: 'Organización de Tareas', description: 'Asocia tareas a cada proyecto y controla su progreso para asegurar que nada se quede atrás.', icon: ClipboardDocumentCheckIcon, colorClasses: 'bg-green-100 text-green-600' }
];

// Array de tecnologías usadas
const techStack = [
    { name: 'Laravel 12', description: 'Backend robusto y escalable.', logoComponent: LaravelLogoRed }, // Usa el logo rojo específico
    { name: 'Vue.js 3', description: 'Interfaz reactiva y moderna.', logoComponent: VueLogo },
    { name: 'PostgreSQL', description: 'Base de datos relacional avanzada y robusta.', logoComponent: SQLogo },
    { name: 'Tailwind CSS', description: 'Framework CSS de utilidad.', logoComponent: TailwindLogo },
    { name: 'Sass', description: 'Preprocesador CSS potente.', logoComponent: SassLogo },
];
</script>

<template>
    <Head title="Bienvenido a CRM Freelancer" />

    <!-- Contenedor principal con clase SCSS -->
    <div class="welcome-page-container">

        <!-- Header con imagen personal en lugar del logo -->
        <header class="welcome-header grid grid-cols-2 items-center gap-2 py-6 lg:grid-cols-3">
            <div class="flex lg:col-start-2 lg:justify-center">
                <!-- Reemplazo del logo por tu imagen -->
                <Link href="/">
                    <img
                        src="/img/logo-crm.webp" 
                        alt="Your Name"                 
                        class="h-10 w-auto"
                        loading="lazy"
                     />
                </Link>
            </div>
            <!-- Navegación (login/register/dashboard) -->
            <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                 <Link v-if="page.props.auth.user" :href="route('dashboard')">Panel de usuario</Link>
                 <template v-else>
                     <Link :href="route('login')">Iniciar Sesión</Link>
                     <Link v-if="canRegister" :href="route('register')" class="ml-4">Registrarse</Link>
                 </template>
            </nav>
        </header>

        <main class="flex-grow w-full">
            <!-- Sección Hero -->
            <section class="welcome-hero">
                 <h1 class="welcome-hero__title">Organiza tu 
                    <span class="bg-gradient-to-r from-indigo-500 to-indigo-700 bg-clip-text text-transparent">
                         Éxito
                     </span> Freelance</h1>
                 <p class="welcome-hero__subtitle">Gestiona tus clientes, proyectos y tareas de forma simple y profesional. Menos caos, más productividad.</p>
                 <div class="welcome-hero__cta-buttons">
                     <Link v-if="canRegister" :href="route('register')">
                         <button type="button" class="cta-button cta-button--primary bg-gradient-to-r from-indigo-500 to-indigo-700 font-semibold hover:from-indigo-600 hover:to-indigo-800">
                            Comenzar Gratis
                            <ArrowRightIcon class="ml-2"/>
                         </button>
                     </Link>
                     <Link v-if="!page.props.auth.user && canLogin" :href="route('login')">
                          <button type="button" class="cta-button cta-button--secondary">
                            Ya tengo cuenta
                         </button>
                     </Link>
                 </div>
             </section>

            <!-- Sección de Características -->
            <section class="features-section">
                 <div class="features-container">
                    <div class="features-section__intro">
                         <span class="intro-eyebrow">Características</span>
                         <h2 class="intro-title">Todo lo que necesitas, simplificado</h2>
                         <p class="intro-description">Funcionalidades esenciales diseñadas para freelancers que valoran la eficiencia.</p>
                    </div>
                    <div class="features-section__grid">
                        <div v-for="feature in features" :key="feature.name" class="feature-card">
                            <div :class="['feature-card__icon-wrapper', feature.colorClasses]">
                                <component :is="feature.icon" class="h-6 w-6" aria-hidden="true" />
                            </div>
                            <h3 class="feature-card__title">{{ feature.name }}</h3>
                            <p class="feature-card__description">{{ feature.description }}</p>
                        </div>
                    </div>
                </div>
            </section>

             <!-- Sección de Tecnologías Usadas -->
            <section class="tech-stack-section">
                 <div class="tech-stack-container">
                     <h2 class="tech-stack-section__title">Construido con Tecnología Moderna</h2>
                     <div class="tech-stack-section__grid">
                         <div v-for="tech in techStack" :key="tech.name" class="tech-card">
                              <div class="tech-card__logo">
                                  <!-- Renderiza el componente SVG correcto -->
                                  <component
                                      :is="tech.logoComponent"
                                      class="h-full w-auto mx-auto"
                                      aria-hidden="true"
                                  />
                              </div>
                              <h4 class="tech-card__name">{{ tech.name }}</h4>
                              <p class="tech-card__description">{{ tech.description }}</p>
                         </div>
                     </div>
                 </div>
            </section>
        </main>

        <!-- Footer con enlace al porfolio -->
        <footer class="welcome-footer">
             {{ new Date().getFullYear() }} © CRM Freelancer realizado por
             <a href="https://josemifdeez.dev" target="_blank" rel="noopener noreferrer" class="font-medium text-indigo-600 hover:text-indigo-800 hover:underline">
                 josemifdeez.dev
             </a>
        </footer>
    </div>
</template>