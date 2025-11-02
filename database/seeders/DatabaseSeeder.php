<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(EmpresaSeeder::class);
        /* $this->call(CreateImageFoldersSeeder::class);
        $this->call(PaginaSeeder::class);
        $this->call(SubtituloSeeder::class);
        $this->call(ParrafoSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ProductoSeeder::class);
        $this->call(PrecioSeeder::class);
        $this->call(HistorialPrecioSeeder::class);
        $this->call(ComponenteSeeder::class);
        $this->call(ClienteSeeder::class); */
    }
}
