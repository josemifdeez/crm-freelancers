<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response // Añadido tipo de retorno para claridad
    {
        $user = Auth::user(); // Obtener usuario autenticado

        // 1. Obtener lista única de empresas del usuario actual
        $companiesList = $user->clients() // Empezar desde los clientes del usuario
            ->whereNotNull('company')
            ->where('company', '!=', '') // Excluir nulos y vacíos
            ->distinct()                 // Obtener valores únicos
            ->orderBy('company')         // Ordenar alfabéticamente
            ->pluck('company')           // Obtener solo la columna 'company'
            ->toArray();                 // Convertir a array simple

        // 2. Construir la consulta base para los clientes del usuario
        $clientsQuery = $user->clients()->orderBy('name'); // Ordenar por nombre por defecto

        // 3. Aplicar filtro de búsqueda si existe
        $clientsQuery->when($request->input('search'), function ($query, $search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        });

        // 4. Aplicar filtro de empresa si existe
        $clientsQuery->when($request->input('company'), function ($query, $company) {
            $query->where('company', $company);
        });

        // 5. Paginar resultados y mantener query string
        $clients = $clientsQuery->paginate(10)->withQueryString(); // Mantener 10 por página como tenías

        // 6. Retornar vista Inertia con todos los datos necesarios
        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            // Pasar filtros activos a la vista para inicializar los inputs/selects
            'filters' => $request->only(['search', 'company']), // Incluir 'company'
            // Pasar la lista de empresas para el desplegable
            'companiesList' => $companiesList,
             // Pasar mensaje flash si existe (opcional, si lo usas en store/update/destroy)
            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Inertia\Response
    {
        return Inertia::render('Clients/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:clients,email,NULL,id,user_id,' . Auth::id(), // Asegurar unicidad por usuario
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        // Asegurar que el cliente se asocia al usuario actual
        $request->user()->clients()->create($validated);
        return Redirect::route('clients.index')->with('success', 'Cliente creado exitosamente.');
    }

    /**
     * Display the specified resource. (Opcional, si tienes vista Show)
     */
    public function show(Client $client): \Inertia\Response
    {
        // Verificar autorización
        if ($client->user_id !== Auth::id()) { abort(403); }
        return Inertia::render('Clients/Show', ['client' => $client]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client): \Inertia\Response
    {
        // Verificar autorización
        if ($client->user_id !== Auth::id()) { abort(403); }
        return Inertia::render('Clients/Edit', ['client' => $client]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client): RedirectResponse
    {
        // Verificar autorización
        if ($client->user_id !== Auth::id()) { abort(403); }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Asegurar unicidad por usuario, ignorando el cliente actual
            'email' => 'nullable|email|max:255|unique:clients,email,' . $client->id . ',id,user_id,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        $client->update($validated);
        return Redirect::route('clients.index')->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): RedirectResponse
    {
        // Verificar autorización
        if ($client->user_id !== Auth::id()) { abort(403); }
        // Considerar lógica adicional si los clientes tienen relaciones (proyectos, tareas)
        $client->delete();
        return Redirect::route('clients.index')->with('success', 'Cliente eliminado exitosamente.');
    }
}