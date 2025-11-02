<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Primero eliminamos la restricción única del email
            $table->dropUnique('clientes_email_unique'); // Nombre automático de la clave única

            // Luego, hacemos que el campo sea nullable
            $table->string('email')->nullable()->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Volvemos a poner el campo como obligatorio y único
            $table->string('email')->unique()->change();
        });
    }
};
