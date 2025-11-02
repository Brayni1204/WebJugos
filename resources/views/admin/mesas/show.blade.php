@extends('adminlte::page')
@section('title', 'Detalle de la Mesa')
@section('content_header')
    <h1>Detalles de la Mesa</h1>
@stop
@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Mesa N° - {{ $mesa->numero_mesa }}</h3>
        </div>
        <div class="card-body">
            <p><strong>ID:</strong> {{ $mesa->id }}</p>
            <p><strong>Número de Mesa:</strong> {{ $mesa->numero_mesa }}</p>
            <p><strong>Estado:</strong>
                <span class="badge {{ $mesa->estado == 'disponible' ? 'bg-success' : 'bg-danger' }}">
                    {{ ucfirst($mesa->estado) }}
                </span>
            </p>
            <p><strong>UUID:</strong> {{ $mesa->uuid }}</p>
            <div class="text-center my-3">
                <h4>Código QR</h4>
                @if ($mesa->codigo_qr)
                    <img id="qrCode" src="{{ $mesa->generarQr() }}" alt="Código QR" width="200">
                @else
                    <p>No disponible</p>
                @endif
            </div>
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.mesas.index') }}" class="btn btn-secondary">Volver a la lista</a>
                @if ($mesa->codigo_qr)
                    <button class="btn btn-success" onclick="imprimirQR()">Imprimir QR</button>
                @endif
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function imprimirQR() {
            let qrImage = document.getElementById("qrCode").src;

            let ventanaImpresion = window.open('', '_blank');
            ventanaImpresion.document.write(`
            <html>
            <head>
                <title>Imprimir Código QR</title>
                <style>
                    body { text-align: center; font-family: Arial, sans-serif; }
                    img { max-width: 300px; margin: 20px auto; }
                    h2 { margin-top: 10px; }
                </style>
            </head>
            <body>
                <h2>Código QR de la Mesa</h2>
                <img src="${qrImage}" alt="Código QR">
                <script>window.print(); window.close();<\/script>
            </body>
            </html>
        `);
            ventanaImpresion.document.close();
        }
    </script>
@stop
