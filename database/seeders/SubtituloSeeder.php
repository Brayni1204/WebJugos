<?php

namespace Database\Seeders;

use App\Models\Subtitulo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubtituloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subtitulo::factory(10)->withImage()->create();
    }
}
