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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('mision');
            $table->text('vision');
            $table->text('mapa_url');
            $table->string('departamento');
            $table->string('provincia');
            $table->string('distrito');
            $table->string('calle');
            $table->text('descripcion')->nullable();
            $table->string('favicon_url')->nullable();
            $table->decimal('delivery', 10, 2);
            $table->string('telefono')->nullable();
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
