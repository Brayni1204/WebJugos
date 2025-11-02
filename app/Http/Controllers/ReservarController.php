<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Mesa;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use LukePOLO\LaraCart\Facades\LaraCart;

class ReservarController extends Controller
{
    public function filtrarPorCategoria($categoria)
    {
        $categoria = Categoria::where('nombre', $categoria)->firstOrFail();
        $productos = Producto::where('categoria_id', $categoria->id)->where('status', 1)->get();
        return view('views.reservar', compact('productos', 'categoria'));
    }

    public function mostrarReservar(Request $request)
    {
        $uuid = $request->query('mesa');
        $categoriaId = $request->query('categoria');

        $mesa = Mesa::where('uuid', $uuid)->first();

        if (!$mesa || $mesa->estado === 'ocupada' || $mesa->status === '0') {
            return redirect()->route('views.index')->with('error', 'Mesa no válida');
        }

        $query = Producto::whereHas('categoria', function ($q) {
            $q->where('status', 1);
        })->where('status', 1);

        if ($categoriaId) {
            $query->where('id_categoria', $categoriaId); // ✅ aquí estaba el error
        }

        $productos = $query->get();
        $categorias = Categoria::where('status', 1)->get();
        $carrito = Session::get('carrito', []);

        return view('views.reservar', compact('productos', 'mesa', 'carrito', 'categorias'));
    }


    public function agregarAlCarrito(Request $request)
    {
        $productoId = $request->input('producto_id');
        $cantidad = $request->input('cantidad', 1);
        $producto = Producto::find($productoId);

        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        $carrito = Session::get('carrito', []);

        if (isset($carrito[$productoId])) {
            $carrito[$productoId]['cantidad'] += $cantidad;
        } else {
            $carrito[$productoId] = [
                'id' => $producto->id,
                'nombre' => $producto->nombre_producto,
                'precio' => $producto->precios->precio_venta ?? 0,
                'cantidad' => $cantidad
            ];
        }

        Session::put('carrito', $carrito);

        return response()->json(['message' => 'Producto agregado al carrito']);
    }

    public function eliminarDelCarrito(Request $request)
    {
        $rowId = $request->input('rowId');
        $item = LaraCart::getItem($rowId);
        if ($item) {
            LaraCart::removeItem($rowId);
            if (LaraCart::getItem($rowId)) {
                return back()->with('alert', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'No se pudo eliminar el producto del carrito.'
                ]);
            }
            session()->flash('alert', [
                'type' => 'success',
                'title' => 'Producto eliminado',
                'message' => 'El producto ha sido eliminado del carrito correctamente.'
            ]);
        } else {
            session()->flash('alert', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'No se encontró el producto en el carrito.'
            ]);
        }
        return redirect()->route('carrito.ver');
    }

    public function vaciarCarrito()
    {
        Session::forget('carrito');
        return response()->json(['message' => 'Carrito vaciado']);
    }
}
