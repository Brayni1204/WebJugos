<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Pagina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\ImageUploadTrait;

class PaginaController extends Controller
{
    use ImageUploadTrait;

    public function index()
    {
        $pagina = Pagina::all();
        return view('admin.paginas.index', compact('pagina'));
    }
    public function create()
    {
        return view('admin.paginas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo_paginas' => 'required|max:255|unique:paginas,titulo_paginas',
            'slug' => 'required|unique:paginas,slug',
            'resumen' => 'nullable|string',
            'imagen' => 'nullable|image',
        ]);


        DB::beginTransaction();
        try {
            $pagina = Pagina::create($request->only(['titulo_paginas', 'slug', 'resumen']));
            $this->storeImage($request, $pagina, 'imagen', 'Pagina');

            DB::commit();
            return redirect()->route('admin.paginas.show', $pagina)->with('info', 'Página creada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la página.']);
        }
    }

    public function show(Pagina $pagina)
    {
        return view('admin.paginas.show', compact('pagina'));
    }

    public function edit(Pagina $pagina)
    {
        return view('admin.paginas.edit', compact('pagina'));
    }

    public function update(Request $request, Pagina $pagina)
    {
        if ($request->has('status')) {
            $pagina->update(['status' => $request->status]);

            return redirect()->route('admin.paginas.show', $pagina)
                ->with('info', 'El estado de la página ha sido actualizado.');
        }
        $request->validate([
            'titulo_paginas' => 'required|max:255|unique:paginas,titulo_paginas,' . $pagina->id,
            'slug' => 'required|unique:paginas,slug,' . $pagina->id,
            'resumen' => 'nullable|string',
            'imagen' => 'nullable|image',
        ]);

        DB::beginTransaction();
        try {
            $pagina->update($request->only(['titulo_paginas', 'slug', 'resumen']));
            $this->updateImage($request, $pagina, 'imagen', 'Pagina');

            DB::commit();
            return redirect()->route('admin.paginas.show', $pagina)
                ->with('info', 'Página actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la página.']);
        }
    }

    public function destroy(Pagina $pagina)
    {
        DB::beginTransaction();
        try {
            if ($pagina->image_pagina) {
                $publicId = $this->extractPublicId($pagina->image_pagina->getRawOriginal('url'));
                if ($publicId) {
                    \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->destroy($publicId);
                }
                $pagina->image_pagina()->delete();
            }

            $pagina->delete();
            DB::commit();
            return response()->json(['success' => 'Página eliminada correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar la página.'], 500);
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
