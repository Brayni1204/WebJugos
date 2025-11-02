<?php

namespace Database\Seeders;

use App\Models\Parrafo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParrafoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Parrafo::factory(10)->withImage()->create();
    }
}
