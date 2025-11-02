<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Precio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductosController extends Controller
{
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
            if ($request->hasFile('imagen')) {
                $url = $request->file('imagen')->store('Producto', 'public');
                $producto->image()->create(['url' => $url]);
            }
            DB::commit();
            return redirect()->route('admin.producto.index')->with('success', 'Producto creado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el producto.'])->withInput();
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
            // **Actualizar datos del producto**
            $producto->update([
                'id_categoria' => $request->id_categoria,
                'nombre_producto' => $request->nombre_producto,
                'descripcion' => $request->descripcion,
                'stock' => $request->stock,
                'status' => $request->status,
            ]);

            // **Actualizar precios**
            $precioCompra = $request->precio_compra !== null ? $request->precio_compra : $request->precio_venta;

            if ($producto->precios) {
                $producto->precios->update([
                    'precio_venta' => $request->precio_venta,
                    'precio_compra' => $precioCompra,
                ]);
            } else {
                // Si el producto no tiene precio registrado, lo creamos
                Precio::create([
                    'id_producto' => $producto->id,
                    'precio_venta' => $request->precio_venta,
                    'precio_compra' => $precioCompra,
                ]);
            }

            // **Actualizar imagen si el usuario sube una nueva**
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($producto->image()->exists()) {
                    Storage::disk('public')->delete($producto->image->first()->url);
                    $producto->image()->delete();
                }

                // Guardar la nueva imagen
                $url = $request->file('imagen')->store('Producto', 'public');
                $producto->image()->create(['url' => $url]);
            }

            DB::commit();
            return redirect()->route('admin.producto.index')->with('success', 'Producto actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el producto.'])->withInput();
        }
    }
    public function destroy(Producto $producto)
    {
        if ($producto->image()->exists()) {
            Storage::disk('public')->delete($producto->image->first()->url);
            $producto->image()->delete();
        }

        $producto->delete();

        return response()->json(['success' => 'Producto eliminado correctamente.']);
    }
}
