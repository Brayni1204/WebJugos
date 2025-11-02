<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }
    public function update(Request $request, User $user)
    {
        try {
            // Sincronizar roles
            $user->roles()->sync($request->roles);

            // Mensaje de éxito
            return redirect()->route('admin.users.edit', $user)->with('success', 'Los roles fueron asignados correctamente.');
        } catch (\Exception $e) {
            // Mensaje de error si algo falla
            return redirect()->route('admin.users.edit', $user)->with('error', 'Hubo un problema al asignar los roles.');
        }
    }
}
