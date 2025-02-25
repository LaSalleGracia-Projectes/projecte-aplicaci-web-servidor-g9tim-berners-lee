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
        Schema::create('peliculas_series', function (Blueprint $table) {
            $table->id('id');
            $table->string('titulo');
            $table->enum('tipo', ['pelicula', 'serie']);
            $table->text('sinopsis')->nullable();
            $table->text('elenco')->nullable();
            $table->year('año_estreno');
            $table->integer('duracion')->nullable();
            $table->string('api_id', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peliculas_series');
    }
};
