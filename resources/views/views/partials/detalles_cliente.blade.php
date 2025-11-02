@foreach ($pedido->detalles as $detalle)
    <div
        style="border-radius: 0.5rem; background-color: #f3f3f3; padding: 8px; width: 6rem; display: flex; flex-direction: column; align-items: center; box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.1);">
        <div style="width: 5rem; height: 5rem; overflow: hidden; border-radius: 0.5rem;">
            @if ($detalle->producto->image->isNotEmpty())
                <img src="{{ asset('storage/' . $detalle->producto->image->first()->url) }}"
                    alt="{{ $detalle->nombre_producto }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <div
                    style="width: 100%; height: 100%; background-color: #ccc; display: flex; align-items: center; justify-content: center;">
                    <span style="color: #666; font-size: 10px;">Sin Imagen</span>
                </div>
            @endif
        </div>
        <div style="text-align: center; font-size: 12px; margin-top: 4px;">
            <strong style="display: block; font-size: 16px;">{{ $detalle->nombre_producto }}</strong>
            <p style="margin: 2px 0; font-size: 13px;">Cantidad: <strong>{{ $detalle->cantidad }}</strong></p>
            <p style="margin: 2px 0; font-size: 16px; color: #702727;">S/{{ number_format($detalle->precio_total, 2) }}
            </p>
        </div>
    </div>
@endforeach
