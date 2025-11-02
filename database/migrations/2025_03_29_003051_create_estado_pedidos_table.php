<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estado_pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade'); // Relación con pedidos
            $table->enum('estado', ['En local', 'En camino', 'En tu Dirección', 'Entregado'])->default('En local'); // Estado del pedido
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estado_pedidos');
    }
};
