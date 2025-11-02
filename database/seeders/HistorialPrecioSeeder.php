<?php

namespace Database\Seeders;

use App\Models\HistorialPrecio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HistorialPrecioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HistorialPrecio::factory(10)->create();
    }
}
