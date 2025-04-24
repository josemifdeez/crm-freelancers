<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Asegúrate que HasFactory está
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Importar User
use App\Models\Project; 

class Client extends Model
{
    use HasFactory; // Esta línea debe estar DENTRO de la clase

    /**
     * The attributes that are mass assignable.
     * IMPORTANTE para poder usar create() o update() con un array.
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // Necesario si lo asignas masivamente
        'name',
        'email',
        'phone',
        'company',
        'notes',
    ];
    
    /**
     * Get the user that owns the Client
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // Un cliente pertenece a un usuario
    }
    
    /**
     * Get all of the projects for the Client
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class); // Un cliente tiene muchos proyectos
    }
}
