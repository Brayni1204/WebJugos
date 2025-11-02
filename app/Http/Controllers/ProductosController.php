<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductosController extends Controller
{
    public function productos(Request $request)
    {
        $categoria_id = $request->input('categoria');

        $categorias = Categoria::where('status', 1)
            ->orderBy('nombre_categoria', 'asc')
            ->get();

        $productos = Producto::with(['image', 'precios'])
            ->where('status', 1)
            ->whereHas('categoria', function ($query) { 
                $query->where('status', 1);
            })
            ->when($categoria_id, function ($query) use ($categoria_id) {
                return $query->where('id_categoria', $categoria_id);
            })
            ->orderBy('created_at', 'asc')
            ->paginate(9);



        return view('views.productos', compact('categorias', 'productos', 'categoria_id'));
    }
}
