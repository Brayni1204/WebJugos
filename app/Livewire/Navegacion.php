<?php

namespace App\Livewire;

use App\Models\Empresa;
use Livewire\Component;
use App\Models\MenuBar;
use App\Models\Pagina;

class Navegacion extends Component
{
    public function render()
    {
        $empresa = Empresa::get();
        $paginas = Pagina::where('status', 2)->get();
        return view('livewire.navegacion', compact('empresa', 'paginas'));
    }
}
