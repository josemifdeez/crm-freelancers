<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project; // Importar Project

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', // Necesario
        'description',
        'status',
    ];
    
    /**
     * Get the project that owns the Task
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class); // Una tarea pertenece a un proyecto
    }
}
