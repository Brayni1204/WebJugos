<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 0px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            width: 80px;
            height: auto;
        }

        .details {
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .details p {
            margin: 5px 0;
        }

        .items {
            margin-bottom: 10px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0px 0;
            border-bottom: 1px dashed #ccc;
        }

        .item:last-child {
            border-bottom: none;
        }

        .item .info {
            flex: 2;
        }

        .item .price {
            text-align: right;
            min-width: 100px;
        }

        .total {
            font-size: 12px;
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h3>Comprobante de Pedido</h3>
        <p><strong>Pedido N°:</strong> 001 - {{ $pedido->id ?? '' }}</p>
        <p><strong>Fecha:</strong> {{ $pedido->created_at ? $pedido->created_at->format('d/m/Y H:i') : '' }}</p>
    </div>

    <div class="details">
        <p><strong>Cliente:</strong> {{ $pedido->cliente->nombre ?? 'Clientes varios' }}</p>
        <p><strong>Método de Entrega:</strong> {{ ucfirst($pedido->metodo_entrega ?? '') }}</p>
    </div>

    <div class="items">
        @foreach ($pedido->detalles as $detalle)
            <p>
                <strong>{{ $detalle->nombre_producto ?? 'N/A' }}</strong><br>
                Cantidad: {{ $detalle->cantidad ?? 0 }} x S/
                {{ number_format($detalle->precio_unitario ?? 0, 2) }}
                = S/
                {{ number_format(($detalle->cantidad ?? 0) * ($detalle->precio_unitario ?? 0), 2) }}
            </p>
        @endforeach
    </div>

    <div class="total">
        <p>S/ {{ number_format($pedido->subtotal ?? 0, 2) }} SubTotal</p>
        <p>S/ {{ number_format($pedido->costo_delivery ?? 0, 2) }} Costo de Delivery</p>
        <p>S/ {{ number_format($pedido->total_pago ?? 0, 2) }} Total a pagar</p>
        @if ($pedido->metodo_pago === 'efectivo')
            @php
                $pago = $pedido->pagos->first(); // Obtener el pago asociado
            @endphp

            @if ($pago)
                <p class="p-0 m-0">Pago con: S/
                    {{ number_format($pago->monto_recibido, 2) }}
                    Vuelto: S/ {{ number_format($pago->vuelto, 2) }}
                </p>
            @else
                <p class="p-0 m-0">Monto recibido: No registrado - Vuelto: No registrado</p>
            @endif
        @endif
    </div>

    <script>
        setTimeout(() => {
            window.print();
            window.close();
        }, 500);
    </script>
</body>

</html>
