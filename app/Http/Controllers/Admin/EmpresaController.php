<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use App\Http\Traits\ImageUploadTrait;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class EmpresaController extends Controller
{
    use ImageUploadTrait;

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
        $request->validate([
            'nombre' => 'required|string|max:255|unique:empresas,nombre,' . $empresa->id,
            'mision' => 'required',
            'vision' => 'required',
            'mapa_url' => 'required|string|max:500',
            'departamento' => 'required',
            'provincia' => 'required',
            'distrito' => 'required',
            'calle' => 'required',
            'telefono' => 'nullable',
            'delivery' => 'required|numeric',
            'favicon' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

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

        if ($request->hasFile('favicon')) {
            if ($empresa->favicon_url) {
                // Assuming the favicon_url is a full Cloudinary URL
                $publicId = $this->extractPublicId($empresa->favicon_url);
                if ($publicId) {
                    Cloudinary::uploadApi()->destroy($publicId);
                }
            }
            $result = Cloudinary::uploadApi()->upload($request->file('favicon')->getRealPath(), [
                'folder' => 'Empresa'
            ]);
            $path = $result['secure_url'];
            $empresa->update(['favicon_url' => $path]);
        }

        $this->updateImage($request, $empresa, 'image', 'Empresa');

        return redirect()->route('admin.empresa.index')->with('success', 'Empresa actualizada correctamente.');
    }
    public function destroy(Empresa $empresa) {}

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
