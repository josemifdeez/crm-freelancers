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
    Schema::create('clients', function (Blueprint $table) {
        $table->id();
        // --- NUESTRAS COLUMNAS ---
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Clave foránea para el usuario dueño del cliente
        $table->string('name');
        $table->string('email')->nullable(); // Puede ser nulo
        $table->string('phone')->nullable(); // Puede ser nulo
        $table->string('company')->nullable(); // Puede ser nulo
        $table->text('notes')->nullable();    // Texto más largo, puede ser nulo
        // -------------------------
        $table->timestamps(); // Columnas created_at y updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
