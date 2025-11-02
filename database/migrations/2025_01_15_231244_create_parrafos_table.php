<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parrafos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_subtitulo');
            $table->text('contenido');
            $table->enum('status', [1, 2])->default(1);
            $table->foreign('id_subtitulo')->references('id')->on('subtitulos')->onDelete('cascade');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('parrafos');
    }
};
