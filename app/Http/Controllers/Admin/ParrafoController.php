<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Pagina;
use App\Models\Parrafo;
use App\Models\Subtitulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParrafoController extends Controller
{
    public function index() {}
    public function create(Request $request)
    {
        $subtitulo = Subtitulo::findOrFail($request->id_subtitulo);
        return view('admin.parrafos.create', compact('subtitulo'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'id_subtitulo' => 'required|exists:subtitulos,id',
            'contenido' => 'required|string',
            'status' => 'required|in:1,2',
            'imagen' => 'nullable|image',
        ]);

        $parrafo = Parrafo::create([
            'id_subtitulo' => $request->id_subtitulo,
            'contenido' => $request->contenido,
            'status' => $request->status,
        ]);

        if ($request->hasFile('imagen')) {
            $url = $request->file('imagen')->store('Parrafo', 'public');
            $parrafo->image()->create(['url' => $url]);
        }

        return redirect()->route('admin.subtitulos.show', $request->id_subtitulo)
            ->with('info', 'Párrafo agregado correctamente.');
    }


    public function show(string $id) {}

    public function edit(Parrafo $parrafo)
    {
        $subtitulo = $parrafo->Subtitulo;
        return view('admin.parrafos.edit', compact('parrafo', 'subtitulo'));
    }
    public function update(Request $request, Parrafo $parrafo)
    {
        $request->validate([
            'id_subtitulo' => 'required|exists:subtitulos,id',
            'contenido' => 'required|string',
            'status' => 'required|in:1,2',
            'imagen' => 'nullable|image',
        ]);
        $parrafo->update([
            'id_subtitulo' => $request->id_subtitulo,
            'contenido' => $request->contenido,
            'status' => $request->status,
        ]);
        if ($request->hasFile('imagen')) {
            if ($parrafo->image) {
                Storage::disk('public')->delete($parrafo->image->url);
                $parrafo->image()->delete();
            }
            $url = $request->file('imagen')->store('Parrafo', 'public');
            $parrafo->image()->create(['url' => $url]);
        }
        return redirect()->route('admin.subtitulos.show', $parrafo->subtitulo->id)
            ->with('info', 'Párrafo actualizado correctamente.');
    }

    public function destroy(string $id) {}
}
