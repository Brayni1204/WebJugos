<?php

namespace Database\Seeders;

use App\Models\Precio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrecioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Precio::factory(10)->create();
    }
}
