<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Categoria::query();
        // Aplicar filtro de búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nombre_categoria', 'like', "%$search%");
        }
        // Paginar las categorías
        $categorias = $query->paginate(10);
        return view('admin.categoria.index', compact('categorias'));
    }
    public function create()
    {
        return view('admin.categoria.create');
    }
    public function store(Request $request)
    {
        // Validación de datos
        $request->validate([
            'nombre_categoria' => 'required|unique:categorias,nombre_categoria|max:100',
            'descripcion'      => 'nullable|string',
            'status'           => 'required|in:1,2',
            'imagen'           => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        // Crear nueva categoría
        $categorium = Categoria::create([
            'nombre_categoria' => $request->nombre_categoria,
            'descripcion'      => $request->descripcion,
            'status'           => $request->status,
        ]);

        // Si hay imagen, guardarla
        if ($request->hasFile('imagen')) {
            $url = $request->file('imagen')->store('Categoria', 'public');
            $categorium->image()->create(['url' => $url]);
        }

        return redirect()
            ->route('admin.categoria.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function show(Categoria $categorium)
    {
        return view('admin.categoria.show', compact('categorium'));
    }
    public function edit(Categoria $categorium)
    {
        return view('admin.categoria.edit', compact('categorium'));
    }
    public function update(Request $request, Categoria $categorium)
    {
        $request->validate([
            'nombre_categoria' => 'required|max:100|unique:categorias,nombre_categoria,' . $categorium->id,
            'descripcion'      => 'nullable|string',
            'status'           => 'required|in:1,2',
            'imagen'           => 'nullable|image|mimes:jpg,png,jpeg'
        ]);
        $categorium->update([
            'nombre_categoria' => $request->nombre_categoria,
            'descripcion'      => $request->descripcion,
            'status'           => $request->status,
        ]);
        if ($request->hasFile('imagen')) {
            if ($categorium->image->count()) {
                Storage::disk('public')->delete($categorium->image->first()->url);
                $categorium->image()->delete();
            }
            $url = $request->file('imagen')->store('Categoria', 'public');
            $categorium->image()->create(['url' => $url]);
        }
        return redirect()
            ->route('admin.categoria.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }
    public function destroy(Categoria $categorium)
    {
        // Borrar imagen si existe
        if ($categorium->image->count()) {
            Storage::disk('public')->delete($categorium->image->first()->url);
            $categorium->image()->delete();
        }

        // Eliminar categoría
        $categorium->delete();

        return response()->json(['success' => 'Categoría eliminada correctamente.']);
    }
}
