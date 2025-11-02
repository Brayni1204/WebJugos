<div id="tabla-ventas">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Subtotal</th>
                <th>Delivery</th>
                <th>Total Pago</th>
                <th>Estado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventas as $venta)
                <tr>
                    <td>{{ $venta->id }}</td>
                    <td>{{ $venta->subtotal }}</td>
                    <td>{{ $venta->costo_delivery }}</td>
                    <td>{{ $venta->total_pago }}</td>
                    <td>{{ $venta->estado }}</td>
                    <td>{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
        {!! $ventas->links('pagination::bootstrap-4') !!}
    </div>
</div>
