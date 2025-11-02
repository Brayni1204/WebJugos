<?php

namespace Database\Seeders;

use App\Models\Componente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComponenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Componente::factory(10)->create();
    }
}
