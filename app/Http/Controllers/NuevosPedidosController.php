<?php

namespace App\Http\Controllers;

use App\Http\Traits\NotificaWebSocket;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\DetallePedido;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use Ratchet\Client\Connector; // 👈 CAMBIA esta línea
use React\EventLoop\Factory;

class NuevosPedidosController extends Controller
{
    use NotificaWebSocket;

    public function verPedido(Request $request)
    {
        $mesaUuid = $request->query('mesa');
        $categoriaId = $request->query('categoria');
        $buscar = $request->query('buscar');

        $mesa = Mesa::where('uuid', $mesaUuid)->first();
        if (!$mesa) {
            return redirect()->route('views.index')->with('error', 'Mesa no encontrada');
        }

        if ($mesa->status === '0') {
            return redirect()->route('views.index')->with('error', 'Mesa está inhabilitada');
        }

        $pedido = Pedido::where('mesa_id', $mesa->id)
            ->whereIn('estado', ['pendiente', 'mesa'])
            ->with('detalles.producto')
            ->first();

        if (!$pedido) {
            return redirect()->route('views.index')->with('error', 'No hay pedidos activos para esta mesa.');
        }

        $query = Producto::with('precios', 'image', 'categoria')
            ->where('status', 1);

        if ($categoriaId) {

            $query->where('id_categoria', $categoriaId);
        }

        if ($buscar) {
            $query->where('nombre_producto', 'like', "%$buscar%");
        }

        // Solo mostrar productos cuyas categorías están activas
        $query->whereHas('categoria', function ($q) {
            $q->where('status', 1);
        });

        $productos = $query->get();
        $categorias = Categoria::where('status', 1)->get();

        return view('views.pedido_detalle', compact('pedido', 'mesa', 'productos', 'categorias'));
    }

    public function store(Request $request)
    {
        if (!isset($request->productos) || count($request->productos) == 0) {
            return response()->json(['error' => 'El carrito está vacío'], 400);
        }
        $mesa = $request->mesa_id ? Mesa::where('uuid', $request->mesa_id)->first() : null;
        if ($mesa && $mesa->estado !== 'disponible') {
            return response()->json(['error' => 'Esta mesa ya está ocupada'], 400);
        }
        $cliente = null;
        if ($request->nombre && $request->correo) {
            $cliente = Cliente::firstOrCreate([
                'email' => $request->correo ?? 'sincorreo@example.com'
            ], [
                'nombre' => $request->nombre  ?? 'Cliente Anónimo',
                'apellidos' => 'No especificado'
            ]);
        }

        // Calcular total
        $subtotal = 0;
        foreach ($request->productos as $producto) {
            $subtotal += $producto['precio'] * $producto['cantidad'];
        }

        $ipCliente = $request->ip();

        // Crear pedido
        $pedido = Pedido::create([
            'mesa_id' => $mesa ? $mesa->id : null,
            'metodo_entrega' => 'mesa',
            'cliente_id' => $cliente ? $cliente->id : null,
            'subtotal' => $subtotal,
            'costo_delivery' => 0,
            'total_pago' => $subtotal,
            'estado' => 'pendiente',
            'ip_cliente' => $ipCliente
        ]);

        foreach ($request->productos as $producto) {
            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $producto['id'],
                'nombre_producto' => $producto['nombre'],
                'cantidad' => $producto['cantidad'],
                'precio_unitario' => $producto['precio'],
                'precio_total' => $producto['cantidad'] * $producto['precio'],
                'descripcion' => $producto['nombre']
            ]);
        }

        if ($mesa) {
            $mesa->update(['estado' => 'ocupada']);
        }

        $this->enviarNotificacion('nuevo', "¡Un cliente ha creado un nuevo pedido!");


        // ✅ En vez de redirigir, enviamos solo el mensaje de éxito
        return response()->json([
            'message' => 'Pedido registrado con éxito',
            'pedido_id' => $pedido->id,
            'mesa_uuid' => $mesa->uuid,
            'redirect' => route('pedido.ver', ['mesa' => $mesa->uuid]) // URL a redirigir
        ]);
    }

    public function actualizarPedido(Request $request, $id)
    {
        try {
            $pedido = Pedido::findOrFail($id);

            // Verificar si el cliente del pedido existe
            if ($pedido->cliente_id) {
                $cliente = Cliente::find($pedido->cliente_id);
                if (!$cliente) {
                    return response()->json(['error' => 'Cliente no encontrado en la base de datos.'], 400);
                }
            }

            // Verificar estado del pedido
            if (!in_array($pedido->estado, ['pendiente', 'mesa'])) {
                return response()->json(['error' => 'No se puede modificar un pedido completado o cancelado.'], 400);
            }

            $subtotalActual = $pedido->subtotal ?? 0;
            $nuevoSubtotal = $subtotalActual;

            foreach ($request->productos as $producto) {
                $detalle = DetallePedido::where('pedido_id', $pedido->id)
                    ->where('producto_id', $producto['id'])
                    ->first();

                if ($detalle) {
                    // Si el producto ya existe en el pedido, solo actualizar la cantidad y total
                    $detalle->cantidad += $producto['cantidad'];
                    $detalle->precio_total = $detalle->cantidad * $detalle->precio_unitario;
                    $detalle->save();
                } else {
                    // Si el producto no existe, agregarlo como nuevo detalle
                    DetallePedido::create([
                        'pedido_id' => $pedido->id,
                        'producto_id' => $producto['id'],
                        'nombre_producto' => $producto['nombre'],
                        'cantidad' => $producto['cantidad'],
                        'precio_unitario' => $producto['precio'],
                        'precio_total' => $producto['cantidad'] * $producto['precio'],
                        'descripcion' => $producto['nombre']
                    ]);
                }

                // Actualizar el subtotal sumando el precio total del nuevo producto
                $nuevoSubtotal += $producto['precio'] * $producto['cantidad'];
            }

            // Actualizar el subtotal y total del pedido
            $pedido->update([
                'subtotal' => $nuevoSubtotal,
                'total_pago' => $nuevoSubtotal + ($pedido->costo_delivery ?? 0)
            ]);

            $this->enviarNotificacion('actualizado', "Un cliente ha actualizado el pedido #{$id}");


            return response()->json(['success' => 'Pedido actualizado con éxito.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar el pedido.', 'detalle' => $e->getMessage()], 500);
        }
    }

    public function procesarPagoMesa(Request $request, $pedido_id)
    {
        $pedido = Pedido::with('detalles')->findOrFail($pedido_id);

        // Configurar SDK
        MercadoPagoConfig::setAccessToken(env('MP_ACCESS_TOKEN'));

        try {
            $client = new PreferenceClient();
            $items = [];

            foreach ($pedido->detalles as $detalle) {
                $items[] = [
                    'title' => $detalle->producto->nombre ?? 'Producto',
                    'quantity' => $detalle->cantidad,
                    'unit_price' => floatval($detalle->precio_unitario),
                    'currency_id' => 'PEN'
                ];
            }

            // Crear preferencia
            $preference = $client->create([
                "items" => $items,
                "back_urls" => [
                    "success" => route('pago.exitoso', $pedido->id) . "?payment_id={payment_id}",
                    "failure" => route('views.pedidos'),
                    "pending" => route('views.pedidos'),
                ],
                "auto_return" => "approved"
            ]);

            return redirect($preference->init_point);
        } catch (MPApiException $e) {
            return back()->withErrors('Error al procesar el pago con MercadoPago.');
        }
    }

    public function pagoExitosoMesa(Request $request, Pedido $pedido)
    {
        $payment_id = $request->query('payment_id'); // Obtiene el ID del pago
        $metodo_pago = null;

        if ($payment_id) {
            // Consultar el detalle del pago a Mercado Pago
            $response = Http::withToken(env('MP_ACCESS_TOKEN'))
                ->get("https://api.mercadopago.com/v1/payments/{$payment_id}");

            if ($response->successful()) {
                $data = $response->json();
                $mp_metodo = $data['payment_method_id']; // Ej: visa, plin, etc.

                // Traducir al enum de tu base de datos
                $metodo_pago = match ($mp_metodo) {
                    'visa', 'master', 'amex' => 'tarjeta',
                    'yape' => 'yape',
                    'plin' => 'plin',
                    'bank_transfer' => 'transferencia',
                    default => 'efectivo',
                };
            }
        }

        // Actualizar el pedido
        $pedido->update([
            'estado' => 'completado',
            'metodo_pago' => $metodo_pago,
        ]);

        return redirect()->route('views.pedidos')->with('success', 'Pago realizado con éxito.');
    }

    public function obtenerDetallesParaCliente($pedido_id)
    {
        $pedido = Pedido::with('detalles.producto.image')->findOrFail($pedido_id);

        // ✅ AÑADE ->render() AL FINAL DE ESTA LÍNEA
        return view('views.partials.detalles_cliente', compact('pedido'))->render();
    }
}
