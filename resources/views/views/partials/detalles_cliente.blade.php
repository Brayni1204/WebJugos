@foreach ($pedido->detalles as $detalle)
    @php
        $descripcion = $detalle->descripcion;
        $nombreBase = $detalle->nombre_producto;
        $customizaciones = '';
        if (str_contains($descripcion, '(')) {
            $parts = explode('(', $descripcion, 2);
            $nombreBase = trim($parts[0]);
            $customizaciones = rtrim(trim($parts[1]), ')');
        }
    @endphp
    <div
        class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:-translate-y-1 transition-transform duration-300">
        <div class="relative">
            @if ($detalle->producto->image->isNotEmpty())
                <img class="h-32 w-full object-cover"
                    src="{{ asset('storage/' . $detalle->producto->image->first()->url) }}"
                    alt="{{ $nombreBase }}">
            @else
                <div
                    class="h-32 w-full bg-gray-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l-1.586-1.586a2 2 0 00-2.828 0L6 14m6-6l.01.01">
                        </path>
                    </svg>
                </div>
            @endif
            <div
                class="absolute top-1.5 right-1.5 bg-blue-600 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center shadow-md">
                {{ $detalle->cantidad }}x</div>
        </div>
        <div class="p-3">
            <h4 class="font-semibold text-gray-900 text-sm truncate"
                title="{{ $nombreBase }}">{{ $nombreBase }}</h4>
            @if ($customizaciones)
                <p class="text-gray-600 text-xs mt-1 truncate"
                    title="{{ $customizaciones }}">{{ $customizaciones }}</p>
            @endif
            <p class="text-right font-bold text-gray-800 text-base mt-2">
                S/{{ number_format($detalle->precio_total, 2) }}</p>
        </div>
    </div>
@endforeach