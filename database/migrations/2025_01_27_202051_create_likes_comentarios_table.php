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
        Schema::create('likes_comentarios', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_comentario');
            $table->enum('tipo', ['like', 'dislike']);
            $table->foreign('id_usuario')->references('id')->on('usuarios');
            $table->foreign('id_comentario')->references('id')->on('comentarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes_comentarios');
    }
};
