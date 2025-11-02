<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\NotificaWebSocket;
use App\Models\Categoria;
use App\Models\DetalleVenta;
use App\Models\Empresa;
use App\Models\Mesa;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Ratchet\Client\Connector; // 👈 CAMBIA esta línea
use React\EventLoop\Factory;

class CreandoNuevosPedidosController extends Controller
{
    use NotificaWebSocket;
    public function index(Request $request)
    {
        $query = Pedido::with('cliente')->orderBy('created_at', 'desc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $pedidos = $query->paginate(10);

        $empresa = Empresa::first();

        return view('admin.nuevospedidosadmin.index', compact('pedidos', 'empresa'));
    }

    public function create()
    {
        $productosventa = Producto::whereHas('categoria', function ($query) {
            $query->where('status', 1);
        })->where('status', 1)->get(); // Solo productos activos
        $categoriasventa = Categoria::where('status', 1)->get();
        $mesasdisponibles = Mesa::where('estado', 'disponible')->where('status', '1')->get();
        return view('admin.nuevospedidosadmin.create', compact('productosventa', 'categoriasventa', 'mesasdisponibles'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'nullable|string|max:255',
                'apellidos' => 'nullable|string|max:255',
                'metodo_entrega' => 'required|string',
                'producto_id' => 'required|exists:productos,id',
                'cantidad' => 'required|integer|min:1',
                'mesa' => 'required|exists:mesas,id',
                'caracteristicas' => 'nullable|array'
            ]);

            // Obtener el producto
            $producto = Producto::findOrFail($request->producto_id);
            $precio = $producto->precios->precio_venta ?? 0;

            // Crear el pedido (cliente_id opcional)
            $pedido = Pedido::create([
                'cliente_id' => null, // No obligatorio
                'mesa_id' => $request->mesa,
                'metodo_entrega' => $request->metodo_entrega,
                'subtotal' => $precio * $request->cantidad,
                'costo_delivery' => 0,
                'total_pago' => $precio * $request->cantidad,
                'estado' => 'pendiente',
            ]);

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

            // Agregar el producto al detalle del pedido
            $pedido->detalles()->create([
                'producto_id' => $producto->id,
                'nombre_producto' => $producto->nombre_producto,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $precio,
                'precio_total' => $precio * $request->cantidad,
                'descripcion' => $descripcion,
            ]);

            Mesa::where('id', $request->mesa)->update(['estado' => 'ocupada']);

            // Responder con JSON para el `fetch()`
            return response()->json([
                'success' => true,
                'redirect' => route('admin.nuevospedidosadmin.edit', $pedido->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Pedido $nuevopedidoadmin)
    {
        $nuevopedidoadmin->load([
            'cliente',
            'mesa',
            'detalles.producto',
            'estadoPedidos',
            'pagos',
            'direccion'
        ]);
        return view('admin.nuevospedidosadmin.show', compact('nuevopedidoadmin'));
    }

    public function cambiarEstado(Pedido $pedido)
    {
        $estados = ['En local', 'En camino', 'En tu Dirección', 'Entregado'];

        // Refrescamos el pedido y los estados
        $pedido = $pedido->fresh('estadoPedidos');

        $estado_actual = $pedido->estadoPedidos->last()->estado ?? $estados[0];
        $indice_actual = array_search($estado_actual, $estados);

        if ($indice_actual !== false && $indice_actual < count($estados) - 1) {
            $nuevo_estado = $estados[$indice_actual + 1];

            $pedido->estadoPedidos()->create([
                'estado' => $nuevo_estado,
                'created_at' => now()
            ]);

            // Refrescamos otra vez para asegurar que ya tiene el nuevo estado
            $pedido = $pedido->fresh('estadoPedidos');

            return response()->json([
                'success' => true,
                'nuevo_estado' => $nuevo_estado,
                'fecha' => now()->format('d/m/Y H:i')
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No se puede actualizar más el estado']);
    }

    public function edit(Pedido $nuevopedidoadmin)
    {
        // Cargar detalles del pedido
        $nuevopedidoadmin->load('detalles');
        if (!$nuevopedidoadmin) {
            abort(404, 'Pedido no encontrado');
        }
        $productosventa = Producto::whereHas('categoria', function ($query) {
            $query->where('status', 1);
        })->where('status', 1)->get(); // Solo productos activos

        $categoriasventa = Categoria::where('status', 1)->get();
        $empresa = Empresa::first();
        return view('admin.nuevospedidosadmin.edit', compact('empresa', 'nuevopedidoadmin', 'productosventa', 'categoriasventa'));
    }

    public function obtenerComprobante($id)
    {
        try {
            $pedido = Pedido::with(['cliente', 'detalles.producto'])->findOrFail($id);
            return view('admin.nuevospedidosadmin.comprobante', compact('pedido'));
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function obtenerComprobanteDetalle($id)
    {
        try {
            $pedido = Pedido::with(['cliente', 'detalles.producto'])->findOrFail($id);

            $html = view('admin.nuevospedidosadmin.comprobantedetalle', compact('pedido'))->render();

            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Pedido $nuevopedidoadmin)
    {
        // Validar que al menos uno de los dos campos esté presente
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'nombre' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        // Convertir el email a minúsculas
        $email = $request->email ? strtolower($request->email) : null;

        // Buscar si el cliente ya existe por email o por nombre
        $cliente = null;

        if ($email) {
            $cliente = \App\Models\Cliente::whereRaw('LOWER(email) = ?', [$email])->first();
        }

        if (!$cliente && $request->nombre) {
            $cliente = \App\Models\Cliente::whereRaw('LOWER(nombre) = ?', [strtolower($request->nombre)])->first();
        }

        if (!$cliente) {
            // Si el cliente no existe con nombre o email, crearlo
            $cliente = \App\Models\Cliente::create([
                'nombre' => $request->nombre,
                'email' => $email,
            ]);
        } else {
            // Si el cliente existe, actualizar los datos aunque solo cambien en mayúsculas/minúsculas
            $datosActualizados = [];

            if ($request->nombre && strtolower($cliente->nombre) !== strtolower($request->nombre)) {
                $datosActualizados['nombre'] = $request->nombre;
            }

            if ($email && $cliente->email !== $email) {
                $datosActualizados['email'] = $email;
            }

            if (!empty($datosActualizados)) {
                $cliente->update($datosActualizados);
            }
        }

        // Asignar el ID del cliente al pedido
        $nuevopedidoadmin->cliente_id = $cliente->id;
        $nuevopedidoadmin->save();

        return redirect()->back()->with('success', 'Cliente actualizado correctamente en el pedido.');
    }

    public function destroy(Pedido $nuevopedidoadmin) {}

    public function completarPedido(Pedido $pedido, Request $request)
    {
        try {
            if ($pedido->estado === 'completado') {
                return response()->json(['error' => 'El pedido ya está completado.'], 400);
            }

            // Validar que se envió un método de pago
            if (!$request->has('metodo_pago')) {
                return response()->json(['error' => 'Debe seleccionarse un método de pago.'], 400);
            }

            // Actualizar el pedido con estado "completado" y el método de pago seleccionado
            $pedido->update([
                'estado' => 'completado',
                'metodo_pago' => $request->metodo_pago
            ]);

            if ($request->metodo_pago === 'efectivo') {
                Pago::create([
                    'pedido_id' => $pedido->id,
                    'total_pago' => $pedido->total_pago,
                    'monto_recibido' => $request->monto_recibido,
                    'vuelto' => $request->vuelto
                ]);
            }
            // Registrar la venta
            $venta = Venta::create([
                'id_user' => $pedido->id_user,
                'pedido_id' => $pedido->id,
                'cliente_id' => $pedido->cliente_id,
                'subtotal' => $pedido->subtotal,
                'costo_delivery' => $pedido->costo_delivery,
                'total_pago' => $pedido->total_pago,
                'estado' => 'completado',
                'metodo_pago' => $request->metodo_pago
            ]);

            // Registrar detalles de venta
            foreach ($pedido->detalles as $detalle) {
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $detalle->producto_id,
                    'nombre_producto' => $detalle->nombre_producto,
                    'cantidad' => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'precio_total' => $detalle->precio_total,
                    'descripcion' => $detalle->descripcion
                ]);
            }

            // Liberar la mesa si el pedido está asociado a una
            if ($pedido->mesa_id) {
                $mesa = $pedido->mesa;
                if ($mesa) {
                    $mesa->update(['estado' => 'disponible']);
                }
            }

            $this->enviarNotificacion('completado', "El pedido #{$pedido->id} se marcó como completado.");

            return response()->json([
                'success' => true,
                'redirect' => route('admin.nuevospedidosadmin.index') // 🔹 Redirigir a la vista de pedidos nuevos
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al completar el pedido.'], 500);
        }
    }

    public function generarComprobante(Pedido $pedido)
    {
        try {
            $pedido->load(['cliente', 'detalles']);
            $empresa = Empresa::first();
            $pdf = Pdf::loadView('admin.nuevospedidosadmin.comprobante', compact('pedido', 'empresa'));
            return $pdf->stream('Comprobante_Pedido_' . $pedido->id . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al generar el comprobante.'], 500);
        }
    }

    public function cancelarPedido(Pedido $pedido)
    {
        try {
            if ($pedido->estado === 'cancelado') {
                return response()->json(['error' => 'El pedido ya está cancelado.'], 400);
            }
            $pedido->update(['estado' => 'cancelado']);
            if ($pedido->mesa_id) {
                $mesa = $pedido->mesa;
                if ($mesa) {
                    $mesa->update(['estado' => 'disponible']);
                }
            }
            $this->enviarNotificacion('cancelado', "El pedido #{$pedido->id} ha sido cancelado.");
            return response()->json(['success' => 'Pedido cancelado con éxito.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cancelar el pedido.'], 500);
        }
    }
    public function actualizarTabla()
    {
        $pedidos = Pedido::with('cliente')->orderBy('created_at', 'desc')->paginate(10);

        // Devuelve solo la vista parcial de la tabla
        return view('admin.nuevospedidosadmin.partials.tabla_pedidos', compact('pedidos'))->render();
    }
    private function enviarNotificacion($mensaje)
    {
        try {
            // Este es el nuevo código para enviar el mensaje con la nueva librería
            $loop = Factory::create();
            $connector = new Connector($loop);

            $connector('ws://127.0.0.1:8090')->then(function ($conn) use ($mensaje) {
                $conn->send($mensaje);
                $conn->close();
            }, function ($e) {
                // \Log::error("No se pudo conectar: {$e->getMessage()}");
            });

            $loop->run();
        } catch (\Exception $e) {
            // \Log::error('Error general de WebSocket: ' . $e->getMessage());
        }
    }
}
