@extends('adminlte::page')

@section('title', 'Detalle de Venta')

@section('content_header')
    <h1>Detalle de Venta 001- </h1>
@stop

@section('content')
    {{-- <div class="card">
        <div class="card-body">
            <!-- Información de la venta -->
            <h4><strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'Cliente no especificado' }}</h4>
            <h4><strong>Fecha de Venta:</strong>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i') }}</h4>

            <h4><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</h4>
            <h4><strong>Estado:</strong>
                <span class="badge {{ $venta->status == 1 ? 'bg-success' : 'bg-danger' }}">
                    {{ $venta->status == 1 ? 'Activa' : 'Cancelada' }}
                </span>
            </h4>

            <!-- Tabla de productos vendidos -->
            <h4 class="mt-4">Productos Vendidos</h4>
            <div class="table-responsive">
                <table class="table table-striped">
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
                                <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td>${{ number_format($detalle->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Botón para regresar -->
            <div class="mt-3">
                <a href="{{ route('admin.ventas.index') }}" class="btn btn-primary">Regresar</a>
            </div>
        </div>
    </div> --}}
@stop
