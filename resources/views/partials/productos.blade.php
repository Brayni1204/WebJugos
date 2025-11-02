@foreach ($productos as $producto)
    <button
        onclick="agregarAlCarrito({{ $producto->id }}, '{{ $producto->nombre_producto }}', {{ $producto->precios->precio_venta ?? 0 }})"
        class="rounded-lg bg-gray-100 border border-gray-300 p-4 cursor-pointer flex flex-col items-center shadow-md transition-transform hover:scale-105">
        <div class="w-56 h-64 overflow-hidden rounded-lg">
            @if ($producto->image->isNotEmpty())
                <img src="{{ asset('storage/' . $producto->image->first()->url) }}" alt="{{ $producto->nombre_producto }}"
                    class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gray-300 flex items-center justify-center text-gray-500">
                    <span>Imagen No Disponible</span>
                </div>
            @endif
        </div>
        <div class="text-center mt-2">
            <strong class="text-lg">{{ $producto->nombre_producto }}</strong>
            <p class="text-gray-700">S/{{ $producto->precios->precio_venta ?? 'N/A' }}</p>
        </div>
    </button>
@endforeach
