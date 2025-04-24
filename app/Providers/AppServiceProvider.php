<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
// -------------------------

use Illuminate\Support\Facades\Vite; // Mantener esta si ya estaba
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // No necesitas añadir nada aquí normalmente
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // --- AÑADIR ESTE BLOQUE ---
        // Forzar esquema HTTPS en producción si la APP_URL usa https
        // Verifica que APP_URL esté configurada como https://... en Render
        if ($this->app->environment('production')) { // Comprueba si el entorno es 'production'
             // Obtener la URL configurada en config/app.php (que lee de .env)
             $appUrl = config('app.url');
             // Forzar esquema solo si la URL configurada es realmente HTTPS
             if (str_starts_with((string) $appUrl, 'https')) { // Añadido (string) por si acaso config devuelve null
                 URL::forceScheme('https');
             }
        }
        // --- FIN DEL BLOQUE AÑADIDO ---

        Vite::prefetch(concurrency: 3);

    }
}