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
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // Ej: PC-01, LAB-02
            $table->string('nombre');           // Ej: Dell OptiPlex 7090
            $table->text('especificaciones');   // Ej: Core i7, 16GB RAM, SSD 512GB
            $table->string('ubicacion');        // Ej: Laboratorio A - Fila 1
            $table->string('estado')->default('Disponible'); // Disponible | Mantenimiento
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }

    
};
