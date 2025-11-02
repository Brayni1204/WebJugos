<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class NosotrosController extends Controller
{
    public function index()
    {
        $empresa = Empresa::get();
        return view('views.nosotros', compact('empresa'));
    }
}
