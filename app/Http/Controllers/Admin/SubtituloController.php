<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Pagina;
use App\Models\Subtitulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\ImageUploadTrait;
use Illuminate\Support\Facades\DB;

class SubtituloController extends Controller
{
    use ImageUploadTrait;

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

        DB::beginTransaction();
        try {
            $subtitulo = Subtitulo::create($request->only(['id_pagina', 'titulo_subtitulo', 'resumen', 'status']));
            $this->storeImage($request, $subtitulo, 'imagen', 'Subtitulo');
            DB::commit();
            return redirect()->route('admin.subtitulos.show', $subtitulo->id)
                ->with('info', 'Subtítulo creado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el subtítulo.']);
        }
    }

    public function show($subtituloId)
    {
        $subtitulo = Subtitulo::with(['Parrafo', 'Paginas', 'image'])
            ->where('id', $subtituloId)
            ->firstOrFail();

        if (!$subtitulo->Paginas) {
            abort(404, 'La página asociada al subtítulo no existe.');
        }

        $paginaActual = $subtitulo->Paginas;

        $subtitulosRelacionados = Subtitulo::where('id_pagina', $paginaActual->id)
            ->where('id', '!=', $subtituloId)
            ->with('image')
            ->get();

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

        DB::beginTransaction();
        try {
            $subtitulo->update($request->only(['titulo_subtitulo', 'resumen', 'status']));
            $this->updateImage($request, $subtitulo, 'imagen', 'Subtitulo');
            DB::commit();
            return redirect()->route('admin.subtitulos.show', $subtitulo->id)
                ->with('info', 'Subtítulo actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el subtítulo.']);
        }
    }


    public function destroy(Subtitulo $subtitulo)
    {
        $pagina = $subtitulo->Paginas;
        DB::beginTransaction();
        try {
            if ($subtitulo->image) {
                $publicId = $this->extractPublicId($subtitulo->image->getRawOriginal('url'));
                if ($publicId) {
                    \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->destroy($publicId);
                }
                $subtitulo->image()->delete();
            }

            $subtitulo->delete();
            DB::commit();
            return redirect()->route('admin.paginas.show', $pagina)->with('info', 'Subtítulo eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar el subtítulo.']);
        }
    }

    private function extractPublicId($url)
    {
        if (!$url) {
            return null;
        }
        $parts = parse_url($url);
        if (!isset($parts['path'])) {
            return null;
        }
        $path = $parts['path'];

        if (preg_match('/\/upload\/(?:v\d+\/)?(.+)/', $path, $matches)) {
            $publicId = $matches[1];
            // Remove file extension
            $publicId = preg_replace('/\.[^.]*$/', '', $publicId);
            return $publicId;
        }

        return null;
    }
}
