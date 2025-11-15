<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Productos Más Vendidos</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { text-align: center; margin-top: 20px; font-size: 0.8em; color: #555; }
    </style>
</head>
<body>
    @include('admin.reportes.exports.partials.header', ['title' => 'Reporte de Productos Más Vendidos', 'fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin])

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Total Vendido</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productos as $producto)
                <tr>
                    <td>{{ $producto->nombre_producto }}</td>
                    <td>{{ $producto->total_vendido }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado el {{ date('Y-m-d H:i:s') }}
    </div>
</body>
</html>