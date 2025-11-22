<?php

namespace App\Livewire\admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role; // Import Role model

class UserIndex extends Component
{
    use WithPagination;

    public $search = "";
    public $roleFilter = "";
    public $tab = 'employees'; // employees or clients

    protected $paginationTheme = "bootstrap";

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
        $this->resetPage();
    }

    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        $this->dispatch('user-updated', [
            'type' => 'success',
            'title' => 'Estado Actualizado',
            'message' => 'El estado del usuario ha sido actualizado.',
        ]);
    }

    public function render()
    {
        $roles = Role::all();

        $usersQuery = User::with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('email', 'LIKE', '%' . $this->search . '%');
                });
            });

        if ($this->tab === 'employees') {
            $usersQuery->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'cliente');
            });
            if ($this->roleFilter) {
                $usersQuery->whereHas('roles', function ($q) {
                    $q->where('name', $this->roleFilter);
                });
            }
        } else { // clients
            $usersQuery->whereHas('roles', function ($query) {
                $query->where('name', 'cliente');
            });
        }

        $users = $usersQuery->orderBy('name', 'asc')->paginate();

        return view('livewire.admin.user-index', compact('users', 'roles'));
    }
}
