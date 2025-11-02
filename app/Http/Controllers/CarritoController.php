<?php

namespace App\Http\Controllers;

use App\Http\Traits\NotificaWebSocket;
use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Direccion;
use App\Models\EstadoPedido;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use LukePOLO\LaraCart\Facades\LaraCart;
use Illuminate\Support\Facades\Auth;


class CarritoController extends Controller
{
    use NotificaWebSocket;

    public function realizarPedido(Request $request)
    {
        try {
            // Obtener usuario autenticado
            if (Auth::check()) {
                $user = Auth::user();
            } else {
                return redirect()->route('login')->with('error', 'Debes iniciar sesión para realizar un pedido.');
            }

            // Verificar si el cliente ya existe por su email
            $cliente = Cliente::where('email', $request->email)->first();
            if ($cliente) {
                $cliente->update([
                    'nombre' => $request->nombre,
                    'apellidos' => $request->apellidos,
                    'telefono' => $request->telefono
                ]);
            } else {
                $cliente = Cliente::create([
                    'nombre' => $request->nombre,
                    'apellidos' => $request->apellidos,
                    'email' => $request->email,
                    'telefono' => $request->telefono
                ]);
            }

            // Crear el pedido
            $pedido = Pedido::create([
                'metodo_entrega' => $request->metodoEntrega,
                'id_user' => $user->id,
                'cliente_id' => $cliente->id,
                'subtotal' => $request->subtotal,
                'costo_delivery' => $request->metodoEntrega === 'delivery' ? $request->costo_delivery : 0, // Corrección aquí
                'total_pago' => $request->total_pago, // Corrección aquí
                'estado' => 'pendiente'
            ]);

            EstadoPedido::create([
                'pedido_id' => $pedido->id,
                'estado' => 'En local' // Estado inicial por defecto
            ]);
            // Registrar los productos en detalle_pedidos
            foreach ($request->productos as $producto) {
                // Buscar el producto en la BD de manera segura
                $productoDB = Producto::where('nombre_producto', $producto['nombre'])->first();

                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $productoDB ? $productoDB->id : null, // Si no existe, será NULL
                    'nombre_producto' => $producto['nombre'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio'],
                    'precio_total' => $producto['cantidad'] * $producto['precio']
                ]);
            }

            // Si el método de entrega es "delivery", registrar la dirección
            if ($request->metodoEntrega === 'delivery') {
                Direccion::create([
                    'departamento' => $request->departamento,
                    'provincia' => $request->provincia,
                    'distrito' => $request->distrito,
                    'calle' => $request->calle,
                    'numero' => $request->numero,
                    'pedido_id' => $pedido->id
                ]);
            }

            // Vaciar el carrito del usuario
            session()->forget('carrito');
            $this->vaciarCarrito();

            // Redirigir a la vista de pedidos con un mensaje de éxito
            $this->enviarNotificacion('nuevo', "¡Un cliente ha creado un nuevo pedido!");

            return redirect()->route('views.pedidos')->with('success', 'Pedido realizado con éxito.');
        } catch (\Exception $e) {
            // Mostrar el error en pantalla
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function agregarAlCarrito(Request $request)
    {
        $producto = Producto::with('precios')->findOrFail($request->id);
        $precioVenta = $producto->precios ? $producto->precios->precio_venta : 0;
        LaraCart::add(
            $producto->id,
            $producto->nombre_producto,
            $request->cantidad,
            $precioVenta
        );
        session()->flash('alert', [
            'type' => 'success',
            'title' => 'Producto agregado',
            'message' => 'El producto se ha añadido correctamente al carrito.'
        ]);
        return redirect()->back();
    }
    public function verCarrito()
    {
        return view('views.carrito', ['items' => LaraCart::getItems()]);
    }
    public function incrementarCantidad($id)
    {
        $item = LaraCart::getItem($id);
        if ($item) {
            $nuevaCantidad = $item->qty + 1;
            LaraCart::updateItem($id, 'qty', $nuevaCantidad);
        } else {
            session()->flash('alert', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'No se encontró el producto en el carrito.'
            ]);
        }
        return redirect()->route('carrito.ver');
    }
    public function decrementarCantidad($id)
    {
        $item = LaraCart::getItem($id);
        if ($item) {
            if ($item->qty > 1) {
                $nuevaCantidad = $item->qty - 1;
                LaraCart::updateItem($id, 'qty', $nuevaCantidad);
            } else {
                session()->flash('alert', [
                    'type' => 'warning',
                    'title' => 'Cantidad mínima alcanzada',
                    'message' => 'No puedes reducir más la cantidad del producto.'
                ]);
            }
        } else {
            session()->flash('alert', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'No se encontró el producto en el carrito.'
            ]);
        }
        return redirect()->route('carrito.ver');
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
        LaraCart::destroyCart();
        return view('views.carrito', ['items' => LaraCart::getItems()]);
    }
}
