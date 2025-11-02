<?php

namespace App\Http\Controllers;

use App\Models\Parrafo;
use App\Models\Subtitulo;

class ParrafoController extends Controller
{
    public function parrafo($pagina, $subtituloId)
    {
        // Obtener el subtítulo con su página y párrafos
        $subtitulo = Subtitulo::with(['Parrafo', 'Paginas'])
            ->where('id', $subtituloId)
            ->where('status', 2)
            ->firstOrFail();

        // Verificar si el subtítulo tiene página asociada
        if (!$subtitulo->Paginas) {
            abort(404, 'La página asociada al subtítulo no existe.');
        }

        // Obtener la página actual
        $paginaActual = $subtitulo->Paginas;

        // Obtener los demás subtítulos asociados a esta página
        $subtitulosRelacionados = Subtitulo::where('id_pagina', $paginaActual->id)
            ->where('id', '!=', $subtituloId)
            ->with('image')
            ->where('status', 2)
            ->get();

        return view('views.parrafo', compact('subtitulo', 'pagina', 'subtitulosRelacionados'));
    }


    public function parrafos($pagina, $subtituloId)
    {
        $subtitulo = Subtitulo::with('Parrafo')
            ->where('id', $subtituloId)
            ->where('status', 2)
            ->firstOrFail();

        return view('views.parrafo', compact('subtitulo', 'pagina'));
    }
}
