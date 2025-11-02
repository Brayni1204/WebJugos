<?php

namespace Database\Seeders;

use App\Models\DetalleVenta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetalleVentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DetalleVenta::factory(10)->create();
    }
}
