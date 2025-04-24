<?php

// 1. Importaciones necesarias
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
// ¡NUEVO! Importa el controlador para la landing (si decides crearlo)
// use App\Http\Controllers\LandingController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 2. Ruta de la Landing Page (PÚBLICA)
Route::get('/', function () {
    // Renderiza un nuevo componente Vue 'Landing.vue' o reutiliza 'Welcome.vue' adaptado
    return Inertia::render('Welcome', [ // O 'Welcome' si lo adaptas
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        // Puedes pasar más datos si tu landing los necesita
    ]);
})->name('landing'); 

// 3. Ruta del Dashboard (Protegida) - Se mantiene igual
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('clients', ClientController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('tasks', TaskController::class);

    Route::patch('/tasks/{task}/complete', [TaskController::class, 'markAsComplete'])->name('tasks.complete');

}); // Fin del grupo de middleware

// 5. Rutas de Autenticación - Se mantienen igual
require __DIR__.'/auth.php';