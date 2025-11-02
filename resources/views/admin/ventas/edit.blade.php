@extends('adminlte::page')

@section('title', 'Editar Venta')

@section('content_header')
    <h1>Editar Venta #{{ $venta->id }}</h1>
@stop

@section('content')
    {{-- <div class="card">
        <div class="card-body">
            <!-- Selección de Cliente -->
            <h5>Cliente</h5>
            <select id="cliente_id" class="form-control">
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ $cliente->id == $venta->id_cliente ? 'selected' : '' }}>
                        {{ $cliente->nombre }}
                    </option>
                @endforeach
            </select>

            <!-- Listado de Productos en la Venta -->
            <h5 class="mt-4">Productos en la Venta</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="productos-agregados">
                        @foreach ($venta->detalles as $detalle)
                            <tr data-id="{{ $detalle->producto->id }}">
                                <td>{{ $detalle->producto->nombre_producto }}</td>
                                <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td><input type="number" min="1" value="{{ $detalle->cantidad }}"
                                        class="form-control cantidad-producto"></td>
                                <td class="subtotal">${{ number_format($detalle->subtotal, 2) }}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm eliminar-producto"><i
                                            class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Selección de Nuevos Productos -->
            <h5 class="mt-4">Agregar Productos</h5>
            <select id="producto_id" class="form-control">
                <option value="">Selecciona un producto</option>
                @foreach ($productos as $producto)
                    <option value="{{ $producto->id }}" data-nombre="{{ $producto->nombre_producto }}"
                        data-precio="{{ $producto->precios->precio_venta ?? 10 }}">
                        {{ $producto->nombre_producto }} - ${{ number_format($producto->precios->precio_venta ?? 10, 2) }}
                    </option>
                @endforeach
            </select>
            <button id="agregar-producto" class="btn btn-success mt-3">Agregar</button>

            <!-- Total de la Venta -->
            <div class="mt-4 text-end">
                <h4>Total: $<span id="total-venta">{{ number_format($venta->total, 2) }}</span></h4>
                <button id="guardar-cambios" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </div>
    </div> --}}
@stop
{{-- 
@section('js')
    <script>
        let productos = @json($venta->detalles);
        let totalVenta = {{ $venta->total }};

        // Agregar Producto
        document.querySelector('#agregar-producto').addEventListener('click', function() {
            let select = document.querySelector('#producto_id');
            let id = select.value;
            let nombre = select.options[select.selectedIndex].dataset.nombre;
            let precio = parseFloat(select.options[select.selectedIndex].dataset.precio);

            if (id === "") return;

            productos.push({
                id,
                nombre,
                precio,
                cantidad: 1
            });
            actualizarTabla();
        });

        // Actualizar tabla y total
        function actualizarTabla() {
            let tbody = document.querySelector('#productos-agregados');
            tbody.innerHTML = "";
            totalVenta = 0;

            productos.forEach((producto, index) => {
                let subtotal = producto.precio * producto.cantidad;
                totalVenta += subtotal;

                tbody.innerHTML += `
                <tr>
                    <td>${producto.nombre}</td>
                    <td>$${producto.precio.toFixed(2)}</td>
                    <td><input type="number" min="1" value="${producto.cantidad}" class="form-control cantidad-producto" data-index="${index}"></td>
                    <td>$${subtotal.toFixed(2)}</td>
                    <td><button class="btn btn-danger btn-sm eliminar-producto" data-index="${index}"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;
            });

            document.querySelector('#total-venta').textContent = totalVenta.toFixed(2);
        }

        // Guardar cambios
        document.querySelector('#guardar-cambios').addEventListener('click', function() {
            let clienteId = document.querySelector('#cliente_id').value;
            let datosVenta = {
                cliente_id: clienteId,
                total: totalVenta,
                productos
            };

            fetch("{{ route('admin.ventas.update', $venta) }}", {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(datosVenta)
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    window.location.href = "{{ route('admin.ventas.index') }}";
                })
                .catch(error => console.error("Error:", error));
        });
    </script>
@stop
 --}}