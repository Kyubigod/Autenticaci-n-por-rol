<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();$table->foreignId('jugador_x_id')->constrained('users');
            $table->foreignId('jugador_o_id')->nullable()->constrained('users');$table->foreignId('turno_actual_id')->nullable()->constrained('users');
            $table->foreignId('ganador_id')->nullable()->constrained('users');$table->string('tablero', 9)->default('---------'); // 9 guiones = tablero vacío
            $table->string('estado')->default('esperando'); // esperando \vert{} en_curso \vert{} finalizada \vert{} empate \vert{} cancelada$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};