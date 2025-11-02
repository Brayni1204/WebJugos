<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserIndex extends Component
{
    use WithPagination;

    public $search = "";

    protected $paginationTheme = "bootstrap";

    public function updatingSearch()
    {
        $this->resetPage(); // Resetea la paginación cuando se realiza una búsqueda
    }

    public function render()
    {
        $users = User::where('name', 'LIKE', '%' . $this->search . '%')
            ->orWhere('email', 'LIKE', '%' . $this->search . '%')
            ->orderBy('name', 'asc') // Necesario para que paginate() no falle
            ->paginate();

        return view('livewire.admin.user-index', compact('users'));
    }
}
