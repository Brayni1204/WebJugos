<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('direccions', function (Blueprint $table) {
            $table->string('numero')->nullable()->after('calle'); // Agregar campo número
        });

        // Asignar un número aleatorio a los registros existentes
        DB::table('direccions')->update([
            'numero' => DB::raw('FLOOR(100 + RAND() * 900)') // Números entre 100 y 999
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('direccions', function (Blueprint $table) {
            $table->dropColumn('numero');
        });
    }
};
