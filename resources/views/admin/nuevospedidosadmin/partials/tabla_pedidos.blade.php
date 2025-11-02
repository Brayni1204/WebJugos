@foreach ($pedidos as $pedido)
    <tr>
        <td>{{ $pedido->id }}</td>
        <td>{{ $pedido->cliente->nombre ?? 'Datos Reservados' }}</td>
        <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
        <td>S/. {{ number_format($pedido->total_pago, 2) }}</td>
        <td>
            <span
                class="badge @if ($pedido->estado === 'pendiente') bg-warning @elseif($pedido->estado === 'completado') bg-success @else bg-danger @endif">
                {{ ucfirst($pedido->estado) }}
            </span>
            @if ($pedido->estado === 'completado')
                <span class="badge bg-primary ms-2">
                    {{ ucfirst($pedido->metodo_pago) }}
                </span>
            @endif
        </td>
        <td>
            @if ($pedido->metodo_entrega === 'delivery')
                <span class="badge bg-primary">Delivery</span>
            @elseif($pedido->metodo_entrega === 'en local')
                <span class="">Local</span>
            @else
                <span class="badge bg-inherit">
                    Mesa - {{ $pedido->mesa_id ?? 'No asignada' }}
                </span>
            @endif
        </td>
        <td width="200px">
            <div style="display: flex; justify-content: center; gap:2px">
                <a href="{{ route('admin.nuevospedidosadmin.edit', $pedido->id) }}" class="btn btn-sm"
                    style="{{ $pedido->estado === 'cancelado' || $pedido->estado === 'completado' ? 'pointer-events: none; background-color: #B0B0B0; opacity: 0.6;' : '' }}">
                    <i class="fas fa-edit fa-lg" style="color: blue;font-size: 20px"></i>
                </a>
                <a href="{{ route('admin.nuevospedidosadmin.show', $pedido->id) }}" class="btn btn-sm">
                    <i class="fas fa-eye fa-lg" style="color: rgb(255, 123, 0);font-size: 20px"></i>
                </a>
                <form action="{{ route('admin.nuevospedidosadmin.destroy', $pedido) }}" method="POST"
                    class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm"
                        onclick="confirmarCancelacion('{{ route('admin.pedidos.cancelar', $pedido) }}')"
                        style="{{ $pedido->estado === 'completado' ? 'pointer-events: none; background-color: #B0B0B0; opacity: 0.6;' : '' }}"
                        {{ $pedido->estado === 'cancelado' ? 'disabled' : '' }}>
                        <i class="fas fa-times fa-lg" style="color: red; font-size: 20px"></i>
                    </button>
                </form>
                <a class="btn btn-sm" onclick="imprimirPedido({{ $pedido->id }})">
                    <i class="fas fa-save" style="color: blue; font-size: 20px"></i>
                </a>
            </div>
        </td>
    </tr>
@endforeach

<tr>
    <td colspan="7">
        <div class="d-flex justify-content-center">
            {{ $pedidos->links('pagination::bootstrap-4') }}
        </div>
    </td>
</tr>
