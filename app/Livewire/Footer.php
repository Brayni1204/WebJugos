<?php

namespace App\Livewire;

use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\MenuBar;
use App\Models\Pagina;
use Livewire\Component;

class Footer extends Component
{
    public $mostrarTodas = false;

    public function toggleCategorias()
    {
        $this->mostrarTodas = !$this->mostrarTodas;
    }

    public function render()
    {
        $empresa = Empresa::get();
        $paginas = Pagina::where('status', 2)->get();
        $categoria = Categoria::where('status', 1)->orderBy('id', 'desc')->get();

        return view('livewire.footer', compact('empresa', 'paginas', 'categoria'));
    }
}
