<?php

namespace Database\Factories;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Componente>
 */
class ComponenteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_producto' => Producto::all()->random()->id,
            'nombre_componente' => $this->faker->word(),
            'cantidad' => $this->faker->numberBetween(1, 50),
            'status' => $this->faker->randomElement([1, 2]),
        ];
    }
}
