<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();
        $pendingTaskStatus = 'pendiente'; // El estado que define una tarea como "pendiente"

        // --- Contadores (se mantienen) ---
        $clientCount = $user->clients()->count();
        $ongoingProjectCount = Project::whereHas('client', fn($q)=>$q->where('user_id', $user->id))
                                ->where('status', 'en curso')
                                ->count();
        $pendingTaskCount = Task::whereHas('project.client', fn($q)=>$q->where('user_id', $user->id))
                               ->where('status', $pendingTaskStatus)
                               ->count();

        // --- Proyectos Próximos (igual que antes) ---
        $upcomingProjects = Project::whereHas('client', fn($q)=>$q->where('user_id', $user->id))
                                ->whereNotNull('due_date')
                                ->where('due_date', '>=', $now->toDateString())
                                ->where('due_date', '<=', $now->addDays(14)->toDateString())
                                ->where('status', '!=', 'finalizado')
                                ->with('client:id,name')
                                ->orderBy('due_date', 'asc')
                                ->limit(5)
                                ->get(['id', 'name', 'due_date', 'client_id']);

        // --- NUEVO: Proyectos CON Tareas Pendientes (para agrupar) ---
        $projectsWithPendingTasks = Project::whereHas('client', fn($q)=>$q->where('user_id', $user->id))
                                        // Asegurarse de que el proyecto TENGA tareas pendientes
                                        ->whereHas('tasks', fn($q) => $q->where('status', $pendingTaskStatus))
                                        // Cargar SÓLO las tareas pendientes de esos proyectos
                                        ->with(['tasks' => function($query) use ($pendingTaskStatus) {
                                            $query->where('status', $pendingTaskStatus)
                                                  ->orderBy('created_at', 'asc') // Ordenar tareas dentro del proyecto
                                                  ->limit(10); // Limitar tareas por proyecto si es necesario
                                        }, 'client:id,name']) // También cargar el cliente
                                        ->orderBy('name', 'asc') // Ordenar los proyectos alfabéticamente
                                        ->limit(5) // Limitar el número de proyectos mostrados
                                        ->get(['id', 'name', 'client_id']); // Columnas necesarias del proyecto


        Log::info('Dashboard Data Extended', [ /* ... (opcional) ... */ ]);

        return Inertia::render('Dashboard', [
            'clientCount' => $clientCount,
            'ongoingProjectCount' => $ongoingProjectCount,
            'pendingTaskCount' => $pendingTaskCount, // Aún útil para la tarjeta de estadísticas
            'upcomingProjects' => $upcomingProjects,
            // 'pendingTasks' => $pendingTasks, // Ya no pasamos la lista plana
            'projectsWithPendingTasks' => $projectsWithPendingTasks, // Pasamos los proyectos agrupados
        ]);
    }
}