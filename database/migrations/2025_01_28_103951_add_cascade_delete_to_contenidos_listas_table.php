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
        Schema::table('contenidos_listas', function (Blueprint $table) {
            // Primero eliminamos la clave foránea existente
            $table->dropForeign(['id_lista']);

            // Luego la volvemos a crear con onDelete('cascade')
            $table->foreign('id_lista')
                  ->references('id')
                  ->on('listas')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contenidos_listas', function (Blueprint $table) {
            // Eliminamos la clave foránea con cascade
            $table->dropForeign(['id_lista']);

            // La volvemos a crear sin cascade
            $table->foreign('id_lista')
                  ->references('id')
                  ->on('listas');
        });
    }
};
