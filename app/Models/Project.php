<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client; // Importar Client
use App\Models\Task; // Importar Task

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', // Necesario
        'name',
        'status',
        'due_date',
    ];
    
    /**
     * Get the client that owns the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class); // Un proyecto pertenece a un cliente
    }
    
    /**
     * Get all of the tasks for the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class); // Un proyecto tiene muchas tareas
    }
}
