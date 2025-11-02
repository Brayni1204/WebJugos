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
        Schema::create('componentes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_producto');
            /* $table->unsignedBigInteger('id_producto'); */
            $table->string('nombre_componente', 100);
            $table->string('cantidad', 50)->nullable();
            $table->enum('status', [1, 2])->default(1);

            $table->foreign('id_producto')->references('id')->on('productos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('componentes');
    }
};
