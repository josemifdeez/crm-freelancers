<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
        // --- NUESTRAS COLUMNAS ---
            $table->foreignId('project_id')->constrained()->onDelete('cascade'); // Clave foránea para el proyecto al que pertenece
            $table->text('description');
            $table->string('status')->default('todo'); // Ej: todo, doing, done
        // -------------------------
            $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
