<?php

namespace Database\Factories;

use App\Models\HistorialPrecio;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HistorialPrecio>
 */
class HistorialPrecioFactory extends Factory
{
    protected $model = HistorialPrecio::class;

    public function definition(): array
    {
        return [
            'id_producto' => Producto::all()->random()->id,
            'precio_venta' => $this->faker->randomFloat(2, 10, 100),
            'precio_compra' => $this->faker->randomFloat(2, 5, 50),
            'fecha_inicio' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'fecha_fin' => $this->faker->optional()->dateTimeBetween('now', '+1 year'),
        ];
    }
}
