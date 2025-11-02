<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Pagina;
use App\Models\Subtitulo;
use App\Models\TituloPagina;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subtitulo>
 */
class SubtituloFactory extends Factory
{
    protected $model = Subtitulo::class;

    public function definition(): array
    {
        return [
            'id_pagina' => Pagina::all()->random()->id,
            'titulo_subtitulo' => $this->faker->sentence(),
            'resumen' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement([1, 2]),
        ];
    }

    public function withImage()
    {
        return $this->afterCreating(function (Subtitulo $Subtitulo) {
            try {
                $imagePath = $this->faker->image('public/storage/Subtitulo', 640, 480, null, false);
                if (!$imagePath || !file_exists('public/storage/Subtitulo' . $imagePath)) {
                    throw new \Exception('no se genero la imagen');
                }
                $Subtitulo->image()->create([
                    'url' => 'Subtitulo/' . $imagePath,
                ]);
            } catch (\Exception $e) {
                $Subtitulo->image()->create([
                    'url' => 'Subtitulo/Novedad.png',
                ]);
            }
        });
    }
}


/* Novedad.png */