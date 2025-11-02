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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            /* atributos de los productos de la tientas  */
            $table->unsignedBigInteger('id_categoria');
            /* $table->unsignedBigInteger('id_categoria'); */
            $table->string('nombre_producto', 150);
            $table->string('descripcion')->nullable();
            $table->integer('stock');
            $table->enum('status', [1, 2])->default(1);

            $table->foreign('id_categoria')->references('id')->on('categorias')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
