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
        Schema::create('valoraciones', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('id_pelicula');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreign('id_pelicula')->references('id')->on('peliculas_series');
            $table->enum('valoracion', ['like', 'dislike']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valoraciones');
    }
};
