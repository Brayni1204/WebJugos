<?php

namespace Database\Factories;

use App\Models\Precio;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PrecioFactory extends Factory
{
    protected $model = Precio::class;

    public function definition(): array
    {
        return [
            'id_producto' => Producto::all()->random()->id,
            'precio_venta' => $this->faker->randomFloat(2, 10, 100),
            'precio_compra' => $this->faker->randomFloat(2, 5, 50),
        ];
    }
}
