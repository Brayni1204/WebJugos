<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Models\Pedido;
use App\Models\Venta;
use Illuminate\Http\Request;
use Stripe\Stripe;
use \Stripe\Checkout\Session as StripeSession;

class ControllerStripeCarritoMesa extends Controller
{
    public function procesarPagoMesa(Pedido $pedido)
    {

        if ($pedido->estado !== 'pendiente') {
            return redirect()->route('views.index')->with('alert', 'El pedido ya ha sido completado.');
        }
        // Configurar Stripe con la clave secreta
        Stripe::setApiKey(config('services.stripe.secret'));

        // Crear una sesión de pago en Stripe
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'pen', // Moneda en soles
                    'product_data' => [
                        'name' => 'Pedido N° - ' . $pedido->id,
                    ],
                    'unit_amount' => intval($pedido->total_pago * 100), // Convertir a céntimos
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('successPagoMesa.stripe', ['pedido' => $pedido->id]),
            'cancel_url' => route('views.index'),
        ]);

        return redirect($session->url);
    }
    public function pagoExitosoMesa($pedidoId)
    {
        $pedido = Pedido::find($pedidoId);

        if (!$pedido) {
            return redirect()->route('views.index')->with('error', 'Pedido no encontrado.');
        }

        $pedido->update(['estado' => 'completado']);

        $pedido->update([
            'metodo_pago' => 'tarjeta'
        ]);
        // Crear la venta a partir del pedido
        $venta = Venta::create([
            'id_user' => $pedido->id_user,
            'pedido_id' => $pedido->id,
            'cliente_id' => $pedido->cliente_id,
            'subtotal' => $pedido->subtotal,
            'costo_delivery' => $pedido->costo_delivery,
            'total_pago' => $pedido->total_pago,
            'estado' => 'completado'
        ]);

        // Pasar los productos del pedido a detalle de venta
        foreach ($pedido->detalles as $detalle) {
            DetalleVenta::create([
                'venta_id' => $venta->id,
                'producto_id' => $detalle->producto_id,
                'nombre_producto' => $detalle->nombre_producto,
                'cantidad' => $detalle->cantidad,
                'precio_unitario' => $detalle->precio_unitario,
                'precio_total' => $detalle->precio_total
            ]);
        }

        if ($pedido->mesa_id) {
            $mesa = $pedido->mesa;
            if ($mesa) {
                $mesa->update(['estado' => 'disponible']);
            }
        }

        return redirect()->route('views.index')->with('success', 'Pago realizado con éxito. El pedido ha sido convertido en una venta.');
    }
}
