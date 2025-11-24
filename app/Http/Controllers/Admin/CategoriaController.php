<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\ImageUploadTrait;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    use ImageUploadTrait;

    public function index(Request $request)
    {
        $query = Categoria::query();
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nombre_categoria', 'like', "%$search%");
        }
        $categorias = $query->paginate(10);
        return view('admin.categoria.index', compact('categorias'));
    }
    public function create()
    {
        return view('admin.categoria.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre_categoria' => 'required|unique:categorias,nombre_categoria|max:100',
            'descripcion'      => 'nullable|string',
            'status'           => 'required|in:1,2',
            'imagen'           => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        DB::beginTransaction();
        try {
            $categorium = Categoria::create($request->only(['nombre_categoria', 'descripcion', 'status']));
            $this->storeImage($request, $categorium, 'imagen', 'Categoria');
            DB::commit();
            return redirect()->route('admin.categoria.index')->with('success', 'Categoría creada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la categoría: ' . $e->getMessage()])->withInput();
        }
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

        DB::beginTransaction();
        try {
            $categorium->update($request->only(['nombre_categoria', 'descripcion', 'status']));
            $this->updateImage($request, $categorium, 'imagen', 'Categoria');
            DB::commit();
            return redirect()->route('admin.categoria.index')->with('success', 'Categoría actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la categoría: ' . $e->getMessage()])->withInput();
        }
    }
    public function destroy(Categoria $categorium)
    {
        DB::beginTransaction();
        try {
            if ($categorium->image->count()) {
                foreach ($categorium->image as $image) {
                    Storage::delete($image->getRawOriginal('url'));
                }
                $categorium->image()->delete();
            }

            $categorium->delete();
            DB::commit();
            return response()->json(['success' => 'Categoría eliminada correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar la categoría.'], 500);
        }
    }
}
