<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Empresa;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Empresa>
 */
class EmpresaFactory extends Factory
{
    protected $model = Empresa::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => 'Mi Empresa', // Se debe mantener único, por eso se fija
            'mision' => $this->faker->paragraph(2),
            'vision' => $this->faker->paragraph(2),
            /* <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d991.5746252741266!2d-77.87328703697632!3d-6.2243160520935925!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91b6ab0076821d19%3A0x143ae5a86a3ab240!2sCevicheria%20el%20Sabor%20de%20mi%20Sireno!5e0!3m2!1ses!2spe!4v1740323270251!5m2!1ses!2spe" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                 */
            'departamento' => $this->faker->state(),
            'provincia' => $this->faker->city(),
            'distrito' => $this->faker->streetName(),
            'calle' => $this->faker->streetAddress(),
            'descripcion' => $this->faker->paragraph(3),
            'delivery' => $this->faker->randomFloat(2, 5, 10),
            'telefono' => $this->faker->phoneNumber(),
            'favicon_url' => 'Empresa/LogoPrincipal.png',
            'mapa_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d247.8935678840259!2d-77.87290571058354!3d-6.2245034610274965!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x91b6ab3df6b3d439%3A0x6d85af38778bab20!2sEL%20PARAISO!5e0!3m2!1ses!2spe!4v1740325019386!5m2!1ses!2spe" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade' . time(),
            'latitud' => $this->faker->latitude(-6.224558),
            'longitud' => $this->faker->longitude(-77.872837),

        ];
    }

    public function withImage()
    {
        return $this->afterCreating(function (Empresa $menuBar) {
            try {
                $imagePath = $this->faker->image('public/storage/Empresa', 640, 480, null, false);
                if (!$imagePath || !file_exists('public/storage/Empresa/' . $imagePath)) {
                    throw new \Exception('No se generó la imagen');
                }
                $menuBar->image_m()->create([
                    'url' => 'Empresa/' . $imagePath,
                ]);
            } catch (\Exception $e) {
                $menuBar->image_m()->create([
                    'url' => 'Empresa/LogoPrincipal.png', // Imagen predeterminada si falla
                ]);
            }
        });
    }
}
