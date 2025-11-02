<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('metodo_entrega')->nullable()->default('en local');
            $table->foreignId('mesa_id')->nullable()->constrained('mesas')->onDelete('cascade');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('cascade');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('costo_delivery', 10, 2);
            $table->decimal('total_pago', 10, 2);
            $table->enum('estado', ['pendiente', 'pagado', 'cancelado', 'completado', 'mesa'])->default('pendiente');
            $table->timestamps();
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
