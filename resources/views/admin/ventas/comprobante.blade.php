    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Comprobante de Venta</title>
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
        </style>
    </head>

    <body>
        <div class="header">
            <h1>Comprobante de Venta</h1>
            <p><strong>Venta N°: 001 - </strong> {{ $venta->id }}</p>
            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }}</p>
        </div>

        <div class="details">
            <p><strong>Cliente:</strong> {{ $venta->cliente->nombre }}</p>
            <p><strong>Total:</strong> S/. {{ number_format($venta->total, 2) }}</p>
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
                @foreach ($venta->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre_producto }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>S/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td>S/. {{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Total</th>
                    <th>S/. {{ number_format($venta->total, 2) }}</th>
                </tr>
            </tfoot>
        </table>
        <div style="display: flex; justify-content: end;">
            <div>
                @if ($venta->status == 0)
                    <p>Anulada</p>
                @elseif($venta->status == 1)
                    <p>Pendiente</p>
                @else
                    <p>Completada</p>
                @endif

            </div>
        </div>
    </body>

    </html>
