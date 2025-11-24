<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Precio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\ImageUploadTrait;

class ProductosController extends Controller
{
    use ImageUploadTrait;

    public function index(Request $request)
    {
        $query = Producto::with('categoria', 'precios', 'image')->orderBy('id', 'desc');
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nombre_producto', 'like', "%$search%");
        }
        $productos = $query->paginate(7);
        return view('admin.producto.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::orderBy('id', 'desc')->get();
        return view('admin.producto.create', compact('categorias'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'id_categoria' => 'required|exists:categorias,id',
            'nombre_producto' => 'required|max:150|unique:productos,nombre_producto',
            'descripcion' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:1,2',
            'precio_venta' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpg,png,jpeg'
        ]);

        DB::beginTransaction();
        try {
            $producto = Producto::create($request->only(['id_categoria', 'nombre_producto', 'descripcion', 'stock', 'status']));
            Precio::create([
                'id_producto' => $producto->id,
                'precio_venta' => $request->precio_venta,
                'precio_compra' => $request->precio_compra,
            ]);
            
            $this->storeImage($request, $producto, 'imagen', 'Producto');

            DB::commit();
            return redirect()->route('admin.producto.index')->with('success', 'Producto creado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el producto: ' . $e->getMessage()])->withInput();
        }
    }
    public function show(Producto $producto)
    {
        return view('admin.producto.show', compact('producto'));
    }
    public function edit(Producto $producto)
    {
        $categorias = Categoria::all();
        return view('admin.producto.edit', compact('producto', 'categorias'));
    }
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'id_categoria' => 'required|exists:categorias,id',
            'nombre_producto' => 'required|max:150|unique:productos,nombre_producto,' . $producto->id,
            'descripcion' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:1,2',
            'precio_venta' => 'required|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $producto->update($request->only(['id_categoria', 'nombre_producto', 'descripcion', 'stock', 'status']));

            $precioCompra = $request->precio_compra !== null ? $request->precio_compra : $request->precio_venta;

            if ($producto->precios) {
                $producto->precios->update([
                    'precio_venta' => $request->precio_venta,
                    'precio_compra' => $precioCompra,
                ]);
            } else {
                Precio::create([
                    'id_producto' => $producto->id,
                    'precio_venta' => $request->precio_venta,
                    'precio_compra' => $precioCompra,
                ]);
            }

            $this->updateImage($request, $producto, 'imagen', 'Producto');

            DB::commit();
            return redirect()->route('admin.producto.index')->with('success', 'Producto actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el producto: ' . $e->getMessage()])->withInput();
        }
    }
    public function destroy(Producto $producto)
    {
        DB::beginTransaction();
        try {
            if ($producto->image->count()) {
                foreach ($producto->image as $image) {
                    Storage::delete($image->getRawOriginal('url'));
                }
                $producto->image()->delete();
            }

            $producto->delete();
            DB::commit();
            return response()->json(['success' => 'Producto eliminado correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar el producto.'], 500);
        }
    }
}
