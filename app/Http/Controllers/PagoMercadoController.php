<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\MercadoPagoConfig;

class PagoMercadoController extends Controller
{
    public function index()
    {
        // Mostrar vista con botón
        return view('producto');
    }

    public function pagar()
    {
        MercadoPagoConfig::setAccessToken(env('MP_ACCESS_TOKEN'));

        $client = new PreferenceClient();
        $preference = $client->create([
            "items" => [
                [
                    "title" => "Zapatillas Deportivas",
                    "quantity" => 1,
                    "unit_price" => 120.00
                ]
            ],
            "back_urls" => [
                "success" => route('pago.exito'),
                "failure" => route('pago.fallo')
            ],

            "auto_return" => "approved"
        ]);

        return redirect()->away($preference->init_point);
    }

    public function exito()
    {
        return "✅ ¡Pago realizado con éxito!";
    }

    public function fallo()
    {
        return "❌ El pago fue cancelado o falló.";
    }
}
