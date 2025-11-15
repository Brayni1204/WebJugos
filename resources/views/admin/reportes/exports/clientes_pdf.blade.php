<!DOCTYPE html>
<html>

<head>
    <title>Reporte de Clientes Frecuentes</title>
    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.8em;
            color: #555;
        }
    </style>
</head>

<body>
    @include('admin.reportes.exports.partials.header', ['title' => 'Reporte de Clientes Frecuentes', 'fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin])

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Compras Realizadas</th>
                <th>Total Gastado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->nombre }}</td>
                    <td>{{ $cliente->compras_realizadas }}</td>
                    <td>S/ {{ number_format($cliente->total_gastado, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        Generado el {{ date('Y-m-d H:i:s') }}
    </div>
</body>

</html>
