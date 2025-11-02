<?php

namespace App\Http\Controllers;

use App\Models\Pagina;
use App\Models\Producto;

class PaginaController extends Controller
{
    public function pagina($pagina)
    {
        $pagina = Pagina::with([
            'Subtitulo' => function ($query) {
                $query->where('status', 2)
                    ->with(['Parrafo' => function ($query) {
                        $query->where('status', 2);
                    }])
                    ->paginate(3);
            }
        ])
            ->where('titulo_paginas', $pagina)
            ->first();

        if (!$pagina) {
            abort(404, 'Página no encontrada.');
        }

        // 🔹 Obtener productos solo de categorías activas
        $productos = Producto::where('status', 1) // ✅ Solo productos activos
            ->whereHas('categoria', function ($query) {
                $query->where('status', 1); // ✅ Solo categorías activas
            })
            ->with('image') // 🔹 Trae la relación de imágenes
            ->get();


        return view('views.paginas', compact('pagina', 'productos'));
    }
}
