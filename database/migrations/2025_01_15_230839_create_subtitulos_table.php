<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subtitulos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pagina');
            $table->string('titulo_subtitulo', 200);
            $table->text('resumen')->nullable();
            $table->enum('status', [1, 2])->default(1);
            $table->timestamps();
            $table->foreign('id_pagina')->references('id')->on('paginas')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('subtitulos');
    }
};
