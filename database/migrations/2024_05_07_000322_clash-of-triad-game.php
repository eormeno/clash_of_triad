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
        Schema::create('clash_of_triad_game', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_1_id')->constrained();
            $table->foreignId('player_2_id')->constrained();
            $table->integer('ronda');
            $table->integer('puntaje_jugador');
            $table->integer('puntaje_oponente');
            $table->integer('juego_propio');
            $table->integer('juego_oponente');
            $table->string('resultado_ronda');
            $table->string('resultado_juego');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clash_of_triad_game');
    }
};
