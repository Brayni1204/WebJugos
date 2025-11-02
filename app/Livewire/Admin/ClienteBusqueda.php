<?php

namespace App\Livewire\Admin;

use App\Models\Cliente;
use Livewire\Component;
use Livewire\WithPagination;

class ClienteBusqueda extends Component
{
    use WithPagination;

    public $search = "";

    protected $paginationTheme = "bootstrap";

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function setCliente($nombre)
    
    {
        $this->search = $nombre; // Asigna el nombre al campo de búsqueda
    }

    public function render()
    {
        $cliente = Cliente::where('nombre', 'LIKE', '%' . $this->search . '%')
            ->orWhere('email', 'LIKE', '%' . $this->search . '%')
            ->first(); // Obtener solo el primer resultado

        return view('livewire.admin.cliente-busqueda', compact('cliente'));
    }
}
