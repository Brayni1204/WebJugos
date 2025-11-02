<?php

namespace Database\Factories;

use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Image::class;

    public function definition(): array
    {
        $types = [
            'Categoria' => 'categorias',
            'Producto' => 'productos',
            'MenuBar' => 'menu_bars',
            'Pagina' => 'paginas',
            'Parrafo' => 'parrafos',
            'Subtitulo' => 'subtitulos',
        ];
        $type = $this->faker->randomElement(array_keys($types));

        return [
            'url' => $types[$type] . '/' . $this->faker->image('public/storage/' . $types[$type], 640, 480, null, false),
            'imageable_id' => $this->faker->numberBetween(1, 100),
            'imageable_type' => $type,
        ];
    }
}
