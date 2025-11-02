<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresa = Empresa::get();
        return view('admin.empresa.index', compact('empresa'));
    }
    public function create() {}
    public function store(Request $request) {}
    public function show(Empresa $empresa) {}
    public function edit($id)
    {
        $empresa = Empresa::findOrFail($id);
        return view('admin.empresa.edit', compact('empresa'));
    }
    public function update(Request $request, Empresa $empresa)
    {
        // Validación
        $request->validate([
            'nombre' => 'required|string|max:255|unique:empresas,nombre,' . $empresa->id,
            'mision' => 'required',
            'vision' => 'required',
            'mapa_url' => 'required|string|max:500', // Permite cualquier URL
            'departamento' => 'required',
            'provincia' => 'required',
            'distrito' => 'required',
            'calle' => 'required',
            'telefono' => 'nullable',
            'delivery' => 'required|numeric',
            'favicon' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        // Asegurar que se actualizan solo los datos necesarios
        $empresa->update($request->only([
            'nombre',
            'mision',
            'vision',
            'mapa_url',
            'departamento',
            'provincia',
            'distrito',
            'calle',
            'telefono',
            'delivery'
        ]));

        // Guardar favicon si hay uno nuevo
        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('Empresa', 'public');
            $empresa->update(['favicon_url' => $path]);
        }

        // Guardar imagen polimórfica
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('Empresa', 'public');
            $empresa->image_m()->updateOrCreate(
                ['imageable_id' => $empresa->id, 'imageable_type' => Empresa::class],
                ['url' => $path]
            );
        }

        // Redirigir con mensaje de éxito
        return redirect()->route('admin.empresa.index')->with('success', 'Empresa actualizada correctamente.');
    }
    public function destroy(Empresa $empresa) {}
}
