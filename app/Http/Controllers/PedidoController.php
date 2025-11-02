<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;


class PedidoController extends Controller
{
    public function verpedido()
    {
        $user = Auth::user();

        // Obtener los pedidos del usuario autenticado con sus detalles
        $pedidos = Pedido::where('id_user', $user->id)
            ->with(['detalles', 'direccion'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('views.pedidos', compact('pedidos'));
    }
    public function generarComprobante(Pedido $pedido)
    {
        try {
            $pedido->load(['cliente', 'detalles']);
            $empresa = Empresa::first();
            $pdf = Pdf::loadView('views.comprobante', compact('pedido', 'empresa'));
            return $pdf->stream('Comprobante_Pedido_' . $pedido->id . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al generar el comprobante.'], 500);
        }
    }

    public function procesarPago(Request $request, $pedido_id)
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

    public function pagoExitoso(Request $request, Pedido $pedido)
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
}
