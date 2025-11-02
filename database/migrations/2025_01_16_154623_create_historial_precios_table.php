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
        Schema::create('historial_precios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_producto');
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('precio_compra', 10, 2);
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();

            $table->foreign('id_producto')->references('id')->on('productos')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_precios');
    }
};
