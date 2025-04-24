<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\Request; // <-- Añadido Request
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * Muestra una lista filtrada de los proyectos.
     */
    public function index(Request $request): \Inertia\Response // <-- Añadido Request y tipo
    {
        $user = Auth::user();
        $searchTerm = $request->input('search');
        $statusFilter = $request->input('status'); // <-- Nuevo filtro de estado

        // Consulta base: proyectos de clientes del usuario
        $projectsQuery = Project::whereHas('client', function ($query) use ($user) {
                                $query->where('user_id', $user->id);
                            })
                            ->with('client:id,name'); // Cargar cliente

        // Aplicar filtro de búsqueda (nombre del proyecto)
        if ($searchTerm) {
            $projectsQuery->where('name', 'like', "%{$searchTerm}%");
        }

        // Aplicar filtro de estado
        if ($statusFilter) {
            $projectsQuery->where('status', $statusFilter);
        }

        // Ordenar y Paginar
        $projects = $projectsQuery->latest('projects.created_at') // Especificar tabla para evitar ambigüedad si se hace join futuro
                                  ->paginate(10)
                                  ->withQueryString(); // Mantener filtros en paginación

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'filters' => $request->only(['search', 'status']), // <-- Pasar ambos filtros
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo proyecto.
     */
    public function create(): \Inertia\Response // <-- Añadido tipo
    {
        $clients = Auth::user()->clients()->select('id', 'name')->get();
        return Inertia::render('Projects/Create', ['clients' => $clients]);
    }

    /**
     * Almacena un nuevo proyecto en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => ['required', Rule::in(['pendiente', 'en curso', 'finalizado'])],
            'due_date' => 'nullable|date',
            'client_id' => ['required', Rule::exists('clients', 'id')->where('user_id', Auth::id())],
        ]);
        Project::create($validated);
        return Redirect::route('projects.index')->with('success', 'Proyecto creado exitosamente.');
    }

    /**
     * Muestra los detalles de un proyecto específico. (Opcional)
     */
    public function show(Project $project): \Inertia\Response // <-- Añadido tipo
    {
        if ($project->client->user_id !== Auth::id()) { abort(403); }
        $project->load('tasks');
        return Inertia::render('Projects/Show', ['project' => $project]);
    }

    /**
     * Muestra el formulario para editar un proyecto existente.
     */
    public function edit(Project $project): \Inertia\Response // <-- Añadido tipo
    {
        if ($project->client->user_id !== Auth::id()) { abort(403); }
        $clients = Auth::user()->clients()->select('id', 'name')->get();
        return Inertia::render('Projects/Edit', ['project' => $project, 'clients' => $clients]);
    }

    /**
     * Actualiza un proyecto existente en la base de datos.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        if ($project->client->user_id !== Auth::id()) { abort(403); }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => ['required', Rule::in(['pendiente', 'en curso', 'finalizado'])],
            'due_date' => 'nullable|date',
            'client_id' => ['required', Rule::exists('clients', 'id')->where('user_id', Auth::id())],
        ]);
        $project->update($validated);
        return Redirect::route('projects.index')->with('success', 'Proyecto actualizado exitosamente.');
    }

    /**
     * Elimina un proyecto de la base de datos.
     */
    public function destroy(Project $project): RedirectResponse
    {
        if ($project->client->user_id !== Auth::id()) { abort(403); }
        $project->delete();
        return Redirect::route('projects.index')->with('success', 'Proyecto eliminado exitosamente.');
    }
}