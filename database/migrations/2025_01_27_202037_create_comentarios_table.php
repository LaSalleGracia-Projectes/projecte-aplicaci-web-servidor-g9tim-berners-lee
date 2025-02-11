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
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_pelicula');
            $table->text('comentario');
            $table->boolean('es_spoiler')->default(false);
            $table->boolean('destacado')->default(false);
            $table->foreign('id_usuario')->references('id')->on('usuarios');
            $table->foreign('id_pelicula')->references('id')->on('peliculas_series');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
