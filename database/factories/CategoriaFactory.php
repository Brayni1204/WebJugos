<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Categoria>
 */
class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        return [
            'nombre_categoria' => $this->faker->unique()->word(),
            'descripcion' => $this->faker->sentence(),
            'status' => $this->faker->numberBetween(1, 2),
        ];
    }
    public function withImage()
    {
        return $this->afterCreating(function (Categoria $Categoria) {
            try {
                // Intentamos generar una imagen con Faker
                $imagePath = $this->faker->image('public/storage/Categoria', 640, 480, null, false);

                // Verificamos que se haya creado la imagen correctamente
                if (!$imagePath || !file_exists('public/storage/Categoria/' . $imagePath)) {
                    throw new \Exception('No se generó la imagen'); // Si no se generó, lanzamos una excepción
                }

                // Guardamos la ruta generada en la base de datos
                $Categoria->image()->create([
                    'url' => 'Categoria/' . $imagePath,
                ]);
            } catch (\Exception $e) {
                // Si hay un error, guardamos la imagen predeterminada
                $Categoria->image()->create([
                    'url' => 'Categoria/Novedad.png', // Cambia esta ruta según tu imagen predeterminada
                ]);
            }
        });
    }
}
