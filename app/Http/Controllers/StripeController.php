<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Venta;
use Stripe\Stripe;
use \Stripe\Checkout\Session as StripeSession;

class StripeController extends Controller
{
    public function procesarPago(Pedido $pedido)
    {
        // Configurar Stripe con la clave secreta
        Stripe::setApiKey(config('services.stripe.secret'));

        // Crear una sesión de pago en Stripe
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'pen', // Moneda en soles
                    'product_data' => [
                        'name' => 'Pedido #' . $pedido->id,
                    ],
                    'unit_amount' => intval($pedido->total_pago * 100), // Convertir a céntimos
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('stripe.success', ['pedido' => $pedido->id]),
            'cancel_url' => route('views.pedidos'),
        ]);
        return redirect($session->url);
    }

    public function pagoExitoso(Pedido $pedido)
    {
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
            'estado' => 'completado' // Estado de la venta
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
        // Redirigir a la vista de pedidos con mensaje de éxito
        return redirect()->route('views.pedidos')->with('success', 'Pago realizado con éxito. El pedido ha sido convertido en una venta.');
    }
} 
