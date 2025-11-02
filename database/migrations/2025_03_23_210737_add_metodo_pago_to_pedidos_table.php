<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->enum('metodo_pago', ['efectivo', 'yape', 'tarjeta', 'transferencia', 'plin'])->nullable();
        });

        // Actualizar registros existentes
        DB::table('pedidos')
            ->where('estado', 'completado')
            ->update(['metodo_pago' => 'efectivo']);

        DB::table('pedidos')
            ->whereIn('estado', ['pendiente', 'cancelado'])
            ->update(['metodo_pago' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn('metodo_pago');
        });
    }
};
