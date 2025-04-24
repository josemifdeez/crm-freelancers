<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request; // <-- Añadido Request
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Muestra una lista filtrada de las tareas.
     */
    public function index(Request $request): \Inertia\Response // <-- Añadido Request y tipo
    {
        $user = Auth::user();
        $searchTerm = $request->input('search');
        $statusFilter = $request->input('status'); // <-- Nuevo filtro de estado

        // Consulta base: tareas de proyectos de clientes del usuario
        $tasksQuery = Task::whereHas('project.client', function ($query) use ($user) {
                            $query->where('user_id', $user->id);
                         })
                         ->with('project:id,name'); // Cargar proyecto

        // Aplicar filtro de búsqueda (descripción de la tarea)
        if ($searchTerm) {
            $tasksQuery->where('description', 'like', "%{$searchTerm}%");
        }

        // Aplicar filtro de estado
        if ($statusFilter) {
             $tasksQuery->where('status', $statusFilter);
        }

        // Ordenar y Paginar
        $tasks = $tasksQuery->latest('tasks.created_at') // Especificar tabla
                           ->paginate(15)
                           ->withQueryString(); // Mantener filtros

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'filters' => $request->only(['search', 'status']), // <-- Pasar ambos filtros
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva tarea.
     */
    public function create(Request $request): \Inertia\Response // <-- Añadido tipo
    {
        $projects = Project::whereHas('client', fn($q) => $q->where('user_id', Auth::id()))
                         ->select('id', 'name')->get();
        $selectedProjectId = $request->query('project_id');
        if ($selectedProjectId && !$projects->contains('id', $selectedProjectId)) { $selectedProjectId = null; }
        return Inertia::render('Tasks/Create', ['projects' => $projects, 'selectedProjectId' => $selectedProjectId]);
    }

    /**
     * Almacena una nueva tarea en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $userClientIds = Auth::user()->clients()->pluck('id')->all();
        $validated = $request->validate([
            'description' => 'required|string',
            'status' => ['required', Rule::in(['pendiente', 'en curso', 'finalizada'])],
            'project_id' => ['required', Rule::exists('projects', 'id')->whereIn('client_id', $userClientIds)],
        ]);
        Task::create($validated);
        return Redirect::route('tasks.index')->with('success', 'Tarea creada exitosamente.');
    }

    /**
     * Muestra los detalles de una tarea específica. (Opcional)
     */
    public function show(Task $task): \Inertia\Response // <-- Añadido tipo
    {
        if ($task->project->client->user_id !== Auth::id()) { abort(403); }
        $task->load('project:id,name');
        return Inertia::render('Tasks/Show', ['task' => $task]);
    }

    /**
     * Muestra el formulario para editar una tarea existente.
     */
    public function edit(Task $task): \Inertia\Response // <-- Añadido tipo
    {
        if ($task->project->client->user_id !== Auth::id()) { abort(403); }
        $projects = Project::whereHas('client', fn($q) => $q->where('user_id', Auth::id()))
                         ->select('id', 'name')->get();
        return Inertia::render('Tasks/Edit', ['task' => $task, 'projects' => $projects]);
    }

    /**
     * Actualiza una tarea existente en la base de datos.
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        if ($task->project->client->user_id !== Auth::id()) { abort(403); }
        $userClientIds = Auth::user()->clients()->pluck('id')->all();
        $validated = $request->validate([
            'description' => 'required|string',
            'status' => ['required', Rule::in(['pendiente', 'en curso', 'finalizada'])],
            'project_id' => ['required', Rule::exists('projects', 'id')->whereIn('client_id', $userClientIds)],
        ]);
        $task->update($validated);
        return Redirect::route('tasks.index')->with('success', 'Tarea actualizada exitosamente.');
    }

    /**
     * Elimina una tarea de la base de datos.
     */
    public function destroy(Task $task): RedirectResponse
    {
       if ($task->project->client->user_id !== Auth::id()) { abort(403); }
       $task->delete();
       return Redirect::back()->with('success', 'Tarea eliminada exitosamente.');
    }

    /**
     * Marca una tarea específica como completada.
     */
    public function markAsComplete(Task $task): RedirectResponse
    {
        if ($task->project->client->user_id !== Auth::id()) { abort(403); }
        $completedStatus = 'finalizada'; 
        if ($task->status !== $completedStatus) {
             $task->update(['status' => $completedStatus]);
             return Redirect::back()->with('success', '¡Tarea marcada como completada!');
        }
        return Redirect::back();
    }
}