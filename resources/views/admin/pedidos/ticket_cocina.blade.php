<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Cocina</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            margin: 0;
            padding: 5px;
        }
        .ticket {
            background: #fff;
            padding: 10px;
        }
        h1 {
            font-size: 1.5em;
            text-align: center;
            margin: 0;
        }
        p {
            margin: 2px 0;
        }
        .productos {
            margin-top: 10px;
            border-top: 1px dashed #000;
        }
        .producto {
            margin-top: 5px;
        }
        .producto p {
            margin: 0;
        }
        .center {
            text-align: center;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <h1>Ticket de Cocina</h1>
        <p class="center">Pedido #{{ $pedido->id }}</p>
        @if($pedido->mesa)
        <p class="center">Mesa: {{ $pedido->mesa->numero_mesa }}</p>
        @endif
        <p class="center">{{ $pedido->created_at->format('d/m/Y H:i') }}</p>
        
        <div class="productos">
            @foreach($pedido->detalles as $detalle)
            <div class="producto">
                <p><strong>Cant: {{ $detalle->cantidad }}</strong></p>
                <p>{{ $detalle->descripcion }}</p>
            </div>
            @endforeach
        </div>
    </div>
    <button class="no-print" onclick="window.print()">Imprimir</button>
</body>
</html>
