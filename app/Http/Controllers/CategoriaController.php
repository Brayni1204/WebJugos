<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Pagina;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categoria = Categoria::where('status', 1)->orderBy('id', 'desc')->paginate(3);
        $paginas = Pagina::with('subtitulos')->get();
        return view('views.index', compact('categoria', 'paginas'));
    }
}
