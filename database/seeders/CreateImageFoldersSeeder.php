<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CreateImageFoldersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $folders = [
            'Categoria',
            'Producto',
            'Pagina',
            'Parrafo',
            'Subtitulo',
            'Empresa',
        ];

        foreach ($folders as $folder) {
            // Verifica si la carpeta no existe y la crea
            if (!Storage::disk('public')->exists($folder)) { // Asegúrate de usar el disco correcto
                Storage::disk('public')->makeDirectory($folder);
                $this->command->info("Carpeta creada: $folder");
            } else {
                $this->command->info("La carpeta ya existe: $folder");
            }
        }
    }
}
