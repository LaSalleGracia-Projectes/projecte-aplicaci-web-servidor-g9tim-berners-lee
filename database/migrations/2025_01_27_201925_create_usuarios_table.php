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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id');
            $table->string('nombre_usuario', 50);
            $table->string('correo', 100)->unique();
            $table->string('contrasena');
            $table->string('foto_perfil')->nullable();
            $table->text('biografia')->nullable();
            $table->enum('rol', ['usuario', 'critico', 'premium'])->default('usuario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
