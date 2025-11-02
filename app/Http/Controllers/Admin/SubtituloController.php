<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pagina;
use App\Models\Subtitulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubtituloController extends Controller
{
    public function index() {}
    public function create(Request $request)
    {
        $pagina = Pagina::findOrFail($request->id_pagina);
        return view('admin.subtitulos.create', compact('pagina'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pagina' => 'required|exists:paginas,id',
            'titulo_subtitulo' => 'required|max:200',
            'resumen' => 'nullable|string',
            'status' => 'required|in:1,2',
            'imagen' => 'nullable|image'
        ]);

        $subtitulo = Subtitulo::create([
            'id_pagina' => $request->id_pagina,
            'titulo_subtitulo' => $request->titulo_subtitulo,
            'resumen' => $request->resumen,
            'status' => $request->status,
        ]);

        if ($request->hasFile('imagen')) {
            $url = $request->file('imagen')->store('Subtitulo', 'public');
            $subtitulo->image()->create(['url' => $url]);
        }

        return redirect()->route('admin.subtitulos.show', $subtitulo->id)
            ->with('info', 'Subtítulo actualizado correctamente.');
    }

    public function show($subtituloId)
    {
        // Obtener el subtítulo con sus párrafos y la página asociada
        $subtitulo = Subtitulo::with(['Parrafo', 'Paginas', 'image'])
            ->where('id', $subtituloId)
            ->firstOrFail();

        // Verificar si el subtítulo tiene una página asociada
        if (!$subtitulo->Paginas) {
            abort(404, 'La página asociada al subtítulo no existe.');
        }

        // Obtener la página actual
        $paginaActual = $subtitulo->Paginas;

        // Obtener los subtítulos relacionados de la misma página, excluyendo el actual
        $subtitulosRelacionados = Subtitulo::where('id_pagina', $paginaActual->id)
            ->where('id', '!=', $subtituloId)
            ->with('image')
            ->get();

        // Retornar la vista con los datos
        return view('admin.subtitulos.show', compact('subtitulo', 'paginaActual', 'subtitulosRelacionados'));
    }

    public function edit(Subtitulo $subtitulo)
    {
        return view('admin.subtitulos.edit', compact('subtitulo'));
    }

    public function update(Request $request, Subtitulo $subtitulo)
    {
        $request->validate([
            'titulo_subtitulo' => 'required|max:200',
            'resumen' => 'nullable|string',
            'status' => 'required|in:1,2',
            'imagen' => 'nullable|image',
        ]);

        // 🔹 Actualizar los datos del subtítulo
        $subtitulo->update([
            'titulo_subtitulo' => $request->titulo_subtitulo,
            'resumen' => $request->resumen,
            'status' => $request->status,
        ]);

        // 🔹 Verificar si se subió una nueva imagen
        if ($request->hasFile('imagen')) {
            // Eliminar la imagen anterior si existe
            if ($subtitulo->image) {
                Storage::disk('public')->delete($subtitulo->image->url);
                $subtitulo->image()->delete(); // Eliminar la referencia en la BD
            }

            // Guardar la nueva imagen
            $url = $request->file('imagen')->store('Subtitulo', 'public');
            $subtitulo->image()->create(['url' => $url]);
        }

        return redirect()->route('admin.subtitulos.show', $subtitulo->id)
            ->with('info', 'Subtítulo actualizado correctamente.');
    }


    public function destroy(Subtitulo $subtitulo)
    {
        // Obtener la página antes de eliminar el subtítulo
        $pagina = $subtitulo->pagina;

        // Eliminar la imagen asociada si existe
        if ($subtitulo->image) {
            Storage::disk('public')->delete($subtitulo->image->url);
            $subtitulo->image()->delete();
        }

        // Eliminar el subtítulo
        $subtitulo->delete();

        // Redirigir de vuelta a la página correspondiente
        return redirect()->route('admin.paginas.show', $pagina)->with('info', 'Subtítulo eliminado correctamente.');
    }
}
