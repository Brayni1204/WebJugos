<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\MenuBar;
use App\Models\Pagina;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pagina>
 */
class PaginaFactory extends Factory
{
    protected $model = Pagina::class;

    public function definition(): array
    {
        $titulo_paginas = $this->faker->unique()->word();
        return [
            'titulo_paginas' => $titulo_paginas,
            'slug' => Str::slug($titulo_paginas),
            'resumen' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement([1, 2]),
        ];
    }

    public function withImage()
    {
        return $this->afterCreating(function (Pagina $Categoria) {
            try {
                $imagePath = $this->faker->image('public/storage/Pagina', 640, 480, null, false);
                if (!$imagePath || !file_exists('public/storage/Pagina' . $imagePath)) {
                    throw new \Exception('no se genero la imagen');
                }
                $Categoria->image_pagina()->create([
                    'url' => 'Pagina/' . $imagePath,
                ]);
            } catch (\Exception $e) {
                $Categoria->image_pagina()->create([
                    'url' => 'Pagina/FondoImg.jpg',
                ]);
            }
        });
    }
}
