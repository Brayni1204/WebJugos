<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Comprobante de Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .details {
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .table th {
            background-color: #f4f4f4;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .estado {
            display: flex;
            justify-content: end;
        }

        .logo {
            width: 100px;
            height: auto;
        }
    </style>
</head>

<body>

    <div class="header">
        <!-- LOGO DE LA EMPRESA -->
        @if ($empresa && $empresa->favicon_url)
            <img src="{{ public_path('storage/' . $empresa->favicon_url) }}" class="logo">
        @endif
        <h1>Comprobante de Pedido</h1>
        <p><strong>Pedido N°: 001 - </strong> {{ $pedido->id }}</p>
        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y H:i') }}</p>
    </div>

    <div class="details">
        <p><strong>Cliente:</strong> {{ $pedido->cliente->nombre }}</p>
        <p><strong>Total:</strong> S/. {{ number_format($pedido->total_pago, 2) }}</p>
        <p><strong>Método de Entrega:</strong> {{ ucfirst($pedido->metodo_entrega) }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pedido->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->nombre_producto }}</td>
                    <td>{{ $detalle->cantidad }}</td>
                    <td>S/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td>S/. {{ number_format($detalle->precio_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Subtotal</th>
                <th>S/. {{ number_format($pedido->subtotal, 2) }}</th>
            </tr>
            <tr>
                <th colspan="3" class="text-right">Costo de Delivery</th>
                <th>S/. {{ number_format($pedido->costo_delivery, 2) }}</th>
            </tr>
            <tr>
                <th colspan="3" class="text-right">Total</th>
                <th>S/. {{ number_format($pedido->total_pago, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="estado">
        <div>
            <strong>Estado: </strong>
            @if ($pedido->estado === 'pendiente')
                <span style="color: orange;">Pendiente</span>
            @elseif($pedido->estado === 'completado')
                <span style="color: green;">Completado</span>
            @elseif($pedido->estado === 'pagado')
                <span style="color: green;">Completado</span>
            @else
                <span style="color: red;">Cancelado</span>
            @endif
        </div>
    </div>

</body>

</html>
