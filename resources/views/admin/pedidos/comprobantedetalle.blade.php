<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Venta</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            background-color: #fff;
            width: 80mm;
            margin: 0 auto;
            padding: 10px;
            box-sizing: border-box;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .receipt-header .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }

        .receipt-header h1 {
            font-size: 24px;
            margin: 0;
            color: #000;
        }

        .receipt-header p {
            margin: 2px 0;
            font-size: 12px;
        }

        .receipt-info,
        .customer-info {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }

        .receipt-info p,
        .customer-info p {
            margin: 0;
            font-size: 12px;
        }

        .receipt-items table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .receipt-items th,
        .receipt-items td {
            padding: 5px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .receipt-items th {
            font-weight: 600;
            background-color: #f9f9f9;
        }

        .receipt-items .text-right {
            text-align: right;
        }

        .receipt-summary {
            margin-top: 20px;
            font-size: 14px;
        }

        .receipt-summary .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }

        .receipt-summary .summary-row.total {
            font-weight: 700;
            font-size: 18px;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
            font-size: 12px;
        }

        .receipt-footer p {
            margin: 0;
        }

        @media print {
            body {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="receipt-header">
            @if ($empresa && $empresa->image_m->first())
                <img src="{{ $empresa->image_m->first()->url }}" alt="Logo" class="logo">
            @endif
            <h1>{{ $empresa->nombre ?? 'Nombre de la Empresa' }}</h1>
            <p>Dirección: {{ $empresa->calle ?? '' }}, {{ $empresa->distrito ?? '' }}, {{ $empresa->provincia ?? '' }}, {{ $empresa->departamento ?? '' }}</p>
            <p>Tel: {{ $empresa->telefono ?? 'N/A' }}</p>
        </div>

        <div class="receipt-info">
            <p><strong>Comprobante de Venta</strong></p>
            <p><strong>N°:</strong> {{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Fecha:</strong> {{ $pedido->created_at->format('d/m/Y H:i A') }}</p>
        </div>

        <div class="customer-info">
            <p><strong>Cliente:</strong> {{ $pedido->cliente->nombre ?? 'Varios' }}</p>
            <p><strong>Entrega:</strong> {{ ucfirst($pedido->metodo_entrega) }}</p>
            @if ($pedido->metodo_entrega === 'mesa')
                <p><strong>Mesa:</strong> {{ $pedido->mesa->numero_mesa ?? 'N/A' }}</p>
            @endif
        </div>

        <div class="receipt-items">
            <table>
                <thead>
                    <tr>
                        <th>Cant.</th>
                        <th>Descripción</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedido->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->cantidad }}</td>
                            <td>
                                {{ $detalle->nombre_producto }}
                                <br>
                                <small>
                                    ({{ $detalle->descripcion }})
                                </small>
                            </td>
                            <td class="text-right">S/ {{ number_format($detalle->precio_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="receipt-summary">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span class="text-right">S/ {{ number_format($pedido->subtotal, 2) }}</span>
            </div>
            @if ($pedido->costo_delivery > 0)
                <div class="summary-row">
                    <span>Costo de Delivery:</span>
                    <span class="text-right">S/ {{ number_format($pedido->costo_delivery, 2) }}</span>
                </div>
            @endif
            <div class="summary-row total">
                <span>TOTAL:</span>
                <span class="text-right">S/ {{ number_format($pedido->total_pago, 2) }}</span>
            </div>

            @php
                $pago = $pedido->pagos->first();
            @endphp
            @if ($pago && $pedido->metodo_pago === 'efectivo')
                <div class="summary-row">
                    <span>Pago con:</span>
                    <span class="text-right">S/ {{ number_format($pago->monto_recibido, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Vuelto:</span>
                    <span class="text-right">S/ {{ number_format($pago->vuelto, 2) }}</span>
                </div>
            @endif

        </div>

        <div class="receipt-footer">
            <p>¡Gracias por su compra!</p>
            <p>www.merakifruit.techinnovats.com</p>
        </div>
    </div>
</body>

</html>