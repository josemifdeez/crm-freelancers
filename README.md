# 📊 Mini CRM para Freelancers

🔗 [Ver página web del proyecto](https://josemifdeez.github.io/stardew-like-game/)

**Una aplicación web SPA sencilla y eficiente para ayudar a los freelancers a gestionar sus clientes, proyectos y tareas diarias.**

## ✨ Funcionalidades Destacadas

*   **Autenticación Segura:** Registro e inicio de sesión de usuarios individuales (Laravel Breeze).
*   **Gestión Multi-usuario:** Cada usuario accede y gestiona únicamente sus propios datos (clientes, proyectos, tareas).
*   **Módulo de Clientes:**
    *   CRUD completo (Crear, Leer, Actualizar, Eliminar).
    *   Campos: Nombre, Email, Teléfono, Empresa, Notas.
*   **Módulo de Proyectos:**
    *   CRUD completo.
    *   Asociación directa a un cliente específico.
    *   Campos: Nombre, Estado (seleccionable: Pendiente, En Curso, Finalizado), Fecha de Entrega.
*   **Módulo de Tareas:**
    *   CRUD completo.
    *   Asociación directa a un proyecto específico.
    *   Campos: Descripción, Estado (seleccionable: Pendiente, En Curso, Finalizado).
*   **Dashboard Interactivo:**
    *   Vista general con estadísticas clave (total clientes, proyectos en curso, tareas pendientes).
    *   Acceso rápido para crear nuevos clientes, proyectos o tareas.
    *   Listado de proyectos con fechas de entrega próximas (vista tipo calendario).
    *   Listado de tareas pendientes agrupadas por proyecto, con opción para marcarlas como completadas directamente.
*   **Interfaz Moderna y Responsiva:**
    *   Single Page Application (SPA) construida con Vue.js e Inertia.js para una experiencia de usuario fluida.
    *   Diseño limpio y profesional usando Tailwind CSS.
    *   Estilos personalizados y organización mantenible gracias a SASS.
*   **Búsqueda y Filtrado:** Funcionalidad de búsqueda por texto y filtrado por estado en las listas de Clientes, Proyectos y Tareas.

## 🛠️ Tecnologías Utilizadas

<p align="left">
  <a href="https://laravel.com" target="_blank" rel="noreferrer"><img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://vuejs.org/" target="_blank" rel="noreferrer"><img src="https://img.shields.io/badge/Vue.js-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white" alt="Vue.js"></a>
  <a href="https://vitejs.dev" target="_blank" rel="noreferrer"><img src="https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite"></a>
  <a href="https://inertiajs.com/" target="_blank" rel="noreferrer"><img src="https://img.shields.io/badge/Inertia.js-9553E9?style=for-the-badge&logo=inertia&logoColor=white" alt="Inertia.js"></a>
  <a href="https://tailwindcss.com/" target="_blank" rel="noreferrer"><img src="https://img.shields.io/badge/Tailwind_CSS-06B6D4?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS"></a>
  <a href="https://sass-lang.com" target="_blank" rel="noreferrer"><img src="https://img.shields.io/badge/Sass-CC6699?style=for-the-badge&logo=sass&logoColor=white" alt="Sass"></a>
  <img src="https://img.shields.io/badge/SQLite-003B57?style=for-the-badge&logo=sqlite&logoColor=white" alt="SQLite" />
  <a href="https://git-scm.com/" target="_blank" rel="noreferrer"><img src="https://img.shields.io/badge/git-%23F05033.svg?style=for-the-badge&logo=git&logoColor=white" alt="Git"></a>
</p>

## 📸 Capturas Destacadas

### 📄 Landing Page
![Captura de pantalla 2025-04-24 171924](https://github.com/user-attachments/assets/6b44af5d-951e-43a8-a017-420fc9b0d15e)

### 👤 Panel de usuario
![Captura de pantalla 2025-04-24 172116](https://github.com/user-attachments/assets/9780cee0-435f-4f2b-aa14-c1ab1e39226b)

## ⚙️ Cómo Ejecutar el Proyecto Localmente

1.  Clona el repositorio:
    `git clone https://github.com/TU_USUARIO/mini-crm-freelancers.git`
    `cd mini-crm-freelancers`

2.  Instala dependencias PHP (Composer):
    `composer install`

3.  Instala dependencias Node.js (NPM):
    `npm install`

4.  Configura el entorno:
    *   Copia el archivo de ejemplo: `cp .env.example .env`
    *   Genera la clave de aplicación: `php artisan key:generate`
    *   Configura la base de datos en el archivo `.env`. Si usas SQLite (recomendado para inicio rápido) y el archivo no existe, créalo: `touch database/database.sqlite`

5.  Ejecuta las migraciones para crear las tablas:
    `php artisan migrate`

6.  Compila assets y ejecuta los servidores de desarrollo:
    *   En una terminal, inicia el compilador de frontend (Vite): `npm run dev`
    *   En **otra** terminal, inicia el servidor de desarrollo de Laravel: `php artisan serve`

7.  Accede a la aplicación: Abre tu navegador y ve a la dirección indicada por `php artisan serve` (normalmente `http://127.0.0.1:8000`).

8.  **Regístrate** como nuevo usuario para comenzar.


