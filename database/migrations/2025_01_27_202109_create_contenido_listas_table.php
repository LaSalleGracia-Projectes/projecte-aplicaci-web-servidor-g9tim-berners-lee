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
        Schema::create('contenidos_listas', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('id_lista');
            $table->integer('tmdb_id');
            $table->string('tipo');
            $table->timestamp('fecha_agregado')->useCurrent();

            $table->foreign('id_lista')->references('id')->on('listas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contenidos_listas');
    }
};
