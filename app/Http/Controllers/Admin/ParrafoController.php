<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Parrafo;
use App\Models\Subtitulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Traits\ImageUploadTrait;
use Illuminate\Support\Facades\DB;

class ParrafoController extends Controller
{
    use ImageUploadTrait;

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

        DB::beginTransaction();
        try {
            $parrafo = Parrafo::create($request->only(['id_subtitulo', 'contenido', 'status']));
            $this->storeImage($request, $parrafo, 'imagen', 'Parrafo');
            DB::commit();
            return redirect()->route('admin.subtitulos.show', $request->id_subtitulo)
                ->with('info', 'Párrafo agregado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el párrafo.']);
        }
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
        
        DB::beginTransaction();
        try {
            $parrafo->update($request->only(['id_subtitulo', 'contenido', 'status']));
            $this->updateImage($request, $parrafo, 'imagen', 'Parrafo');
            DB::commit();
            return redirect()->route('admin.subtitulos.show', $parrafo->subtitulo->id)
                ->with('info', 'Párrafo actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el párrafo.']);
        }
    }

    public function destroy(Parrafo $parrafo)
    {
        $subtitulo = $parrafo->Subtitulo;
        DB::beginTransaction();
        try {
            if ($parrafo->image) {
                $publicId = $this->extractPublicId($parrafo->image->getRawOriginal('url'));
                if ($publicId) {
                    \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::uploadApi()->destroy($publicId);
                }
                $parrafo->image()->delete();
            }

            $parrafo->delete();
            DB::commit();
            return redirect()->route('admin.subtitulos.show', $subtitulo->id)
                ->with('info', 'Párrafo eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar el párrafo.']);
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
