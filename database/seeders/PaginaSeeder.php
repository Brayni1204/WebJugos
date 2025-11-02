<?php

namespace Database\Seeders;

use App\Models\Pagina;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaginaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pagina::factory(10)->withImage()->create();
    }
}
