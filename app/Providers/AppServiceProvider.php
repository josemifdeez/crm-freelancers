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
        if ($this->app->environment('production')) { // Comprueba si el entorno es 'production'
             $appUrl = config('app.url');
             if (str_starts_with((string) $appUrl, 'https')) { // Añadido (string) por si acaso config devuelve null
                 URL::forceScheme('https');
             }
        }
        Vite::prefetch(concurrency: 3);
    }
}