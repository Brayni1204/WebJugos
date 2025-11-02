<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Parrafo;
use App\Models\Subtitulo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Parrafo>
 */
class ParrafoFactory extends Factory
{
    protected $model = Parrafo::class;

    public function definition(): array
    {
        return [
            'id_subtitulo' => Subtitulo::all()->random()->id,
            'contenido' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement([1, 2]),
        ];
    }


    public function withImage()
    {
        return $this->afterCreating(function (Parrafo $Parrafo) {
            try {
                $imagePath = $this->faker->image('public/storage/Parrafo', 640, 480, null, false);
                if (!$imagePath || !file_exists('public/storage/Parrafo' . $imagePath)) {
                    throw new \Exception('no se genero la imagen');
                }
                $Parrafo->image()->create([
                    'url' => 'Parrafo/' . $imagePath,
                ]);
            } catch (\Exception $e) {
                $Parrafo->image()->create([
                    'url' => 'Parrafo/Nosotros.png',
                ]);
            }
        });
    }
}
