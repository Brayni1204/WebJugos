<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Image;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        return [
            'id_categoria' => Categoria::all()->random()->id,
            'nombre_producto' => $this->faker->word(),
            'descripcion' => $this->faker->sentence(),
            'stock' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement([1, 2]),
        ];
    }

    public function withImage()
    {
        return $this->afterCreating(function (Producto $producto) {
            try {
                $imagePath = $this->faker->image('public/storage/Producto', 640, 480, null, false);
                if (!$imagePath || !file_exists('public/storage/Producto' . $imagePath)) {
                    throw new \Exception('no se genero la imagen');
                }
                $producto->image()->create([
                    'url' => 'Producto/' . $imagePath,
                ]);
            } catch (\Exception $e) {
                $producto->image()->create([
                    'url' => 'Producto/jusdorange.jpeg',
                ]);
            }
        });
    }
}
