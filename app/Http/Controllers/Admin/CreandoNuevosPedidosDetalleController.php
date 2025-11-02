<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\NotificaWebSocket;
use App\Models\DetallePedido;
use App\Models\Empresa;
use App\Models\Pedido;
use App\Models\Producto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Ratchet\Client\Connector; // 👈 CAMBIA esta línea
use React\EventLoop\Factory;

class CreandoNuevosPedidosDetalleController extends Controller
{
    use NotificaWebSocket;

    public function index() {}
    public function create() {}
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
            'caracteristicas' => 'nullable|array'
        ]);

        // Obtener el pedido
        $producto = Producto::findOrFail($request->producto_id);
        $pedido = Pedido::findOrFail($request->pedido_id);

        $caracteristicas = $request->input('caracteristicas', []);

        $grupoTemperatura = ['helado', 'temperado', 'temperatura ambiente'];
        $grupoAzucar = ['con azúcar', 'sin azúcar', 'bajo en azúcar'];

        $temperatura = collect($caracteristicas)->first(fn($c) => in_array($c, $grupoTemperatura));
        $azucar = collect($caracteristicas)->first(fn($c) => in_array($c, $grupoAzucar));

        $descripcion = $producto->nombre_producto;
        $extras = [];

        if ($temperatura) $extras[] = $temperatura;
        if ($azucar) $extras[] = $azucar;

        if (count($extras) > 0) {
            $descripcion .= ', ' . implode(' ', $extras);
        }

        // Verificar si el producto ya está en el detalle
        $detalle = DetallePedido::where('pedido_id', $request->pedido_id)
            ->where('producto_id', $request->producto_id)
            ->where('descripcion', $descripcion)
            ->first();

        if ($detalle) {
            // Si el producto ya existe en el pedido, actualizar la cantidad y el total
            $detalle->cantidad += $request->cantidad;
            $detalle->precio_total = $detalle->cantidad * $detalle->precio_unitario;
            $detalle->save();
        } else {
            // Si el producto no existe, crearlo en detalle_pedidos
            $detalle = DetallePedido::create([
                'pedido_id' => $request->pedido_id,
                'producto_id' => $request->producto_id,
                'nombre_producto' =>  Producto::find($request->producto_id)->nombre_producto,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $request->precio_unitario,
                'precio_total' => $request->cantidad * $request->precio_unitario,
                'descripcion' => $descripcion,
            ]);
        }

        // Actualizar el subtotal y total del pedido
        $pedido->subtotal += $request->cantidad * $request->precio_unitario;
        $pedido->total_pago = $pedido->subtotal + $pedido->costo_delivery; // Asumiendo que el costo de delivery ya está establecido
        $pedido->save();

        $this->enviarNotificacion('actualizado', "Se agregaron productos al pedido #{$pedido->id}");


        return response()->json([
            'success' => true,
            'message' => 'Producto agregado o actualizado en el pedido',
            'detalle' => $detalle,
            'pedido' => $pedido
        ]);
    }
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, $id)
    {
        $request->validate([
            'accion' => 'required|in:incrementar,decrementar',
        ]);

        $detalle = \App\Models\DetallePedido::findOrFail($id);
        $pedido = \App\Models\Pedido::findOrFail($detalle->pedido_id);

        if ($request->accion === 'decrementar' && $detalle->cantidad <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes reducir la cantidad por debajo de 1'
            ]);
        }

        if ($request->accion === 'incrementar') {
            $detalle->cantidad += 1;
        } elseif ($request->accion === 'decrementar') {
            $detalle->cantidad -= 1;
        }

        // Recalcular precio total del detalle
        $detalle->precio_total = $detalle->cantidad * $detalle->precio_unitario;
        $detalle->save();

        // Recalcular el subtotal y total del pedido
        $pedido->subtotal = \App\Models\DetallePedido::where('pedido_id', $pedido->id)->sum('precio_total');
        $pedido->total_pago = $pedido->subtotal + $pedido->costo_delivery;
        $pedido->save();
        $this->enviarNotificacion('actualizado', "Se modificó el pedido #{$detalle->pedido_id}");


        return response()->json([
            'success' => true,
            'detalle' => $detalle,
            'pedido' => $pedido
        ]);
    }
    public function obtenerDetallesPedido($pedidoId)
    {
        try {
            $pedido = \App\Models\Pedido::with('detalles.producto')->findOrFail($pedidoId);

            return response()->json([
                'success' => true,
                'html' => view('admin.nuevospedidosadmin.partials.detalles_pedido', compact('pedido'))->render(),
                'total' => number_format($pedido->subtotal, 2) // 🔥 Enviamos el total actualizado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }


    public function destroy($id)
    {
        $detalle = \App\Models\DetallePedido::findOrFail($id);
        $pedido = \App\Models\Pedido::findOrFail($detalle->pedido_id);

        // Restar el subtotal del pedido antes de eliminar el detalle
        $pedido->subtotal -= $detalle->precio_total;
        $pedido->subtotal = max($pedido->subtotal, 0); // Asegurar que no sea negativo

        // Recalcular el total del pedido
        $pedido->total_pago = $pedido->subtotal + $pedido->costo_delivery;

        // Eliminar el detalle del pedido
        $detalle->delete();

        // Guardar los cambios en el pedido
        $pedido->save();

        $this->enviarNotificacion('actualizado', "Se eliminó un producto del pedido #{$detalle->pedido_id}");
        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del pedido',
            'pedido' => $pedido
        ]);
    }
}
