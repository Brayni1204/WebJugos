<tbody id="productosLista" class="text-center">
    @foreach ($pedido->detalles as $detalle)
        <tr data-id="{{ $detalle->id }}">
            <td>{{ $detalle->descripcion }}</td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button type="button" onclick="modificarCantidad('decrementar', {{ $detalle->id }})">➖</button>
                    <span id="cantidad-{{ $detalle->id }}" class="px-2"
                        style="font-size: 25px">{{ $detalle->cantidad }}</span>
                    <button type="button" onclick="modificarCantidad('incrementar', {{ $detalle->id }})">➕</button>
                </div>
            </td>
            <td>S/. {{ number_format($detalle->precio_unitario, 2) }}</td>
            <td id="subtotal-{{ $detalle->id }}">S/. {{ number_format($detalle->precio_total, 2) }}</td>
            <td>
                <button class="btn btn-danger btn-sm eliminar-producto" onclick="eliminarProducto({{ $detalle->id }})">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>
    @endforeach
    <style>
        .btn-group-sm button {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
        }

        .btn-group-sm button:focus {
            outline: none;
        }

        .btn-group-sm span {
            font-size: 10px;
        }
    </style>
</tbody>
