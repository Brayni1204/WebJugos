<x-app-layout>
    <div class="h-screen overflow-auto">
        <div class="mb-10 pb-10 mt-24">
            <div class="container mx-auto p-4 sm:p-6">
                <h1 class="text-2xl font-bold mb-4 text-center sm:text-left">Mis Pedidos</h1>

                @if ($pedidos->isEmpty())
                    <p class="text-gray-600 text-center">No tienes pedidos registrados.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-200 text-sm sm:text-base">
                            <thead>
                                <tr class="bg-gray-100 text-center">
                                    <th class="border border-gray-300 p-1">Fecha</th>
                                    <th class="border border-gray-300 p-1">Entrega</th>
                                    <th class="border border-gray-300 p-1">Subtotal</th>
                                    <th class="border border-gray-300 p-1">Delivery</th>
                                    <th class="border border-gray-300 p-1">Total</th>
                                    <th class="border border-gray-300 p-1">Estado</th>
                                    <th class="border border-gray-300 p-1 text-right ">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pedidos as $pedido)
                                    <tr class="border border-gray-200 cursor-pointer hover:bg-gray-50
                                        @if ($pedido->metodo_entrega === 'delivery') clickable-row @endif"
                                        data-id="{{ $pedido->id }}">
                                        <td class="p-1">{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="p-2">{{ ucfirst($pedido->metodo_entrega) }}</td>
                                        <td class="p-2">{{ number_format($pedido->subtotal, 2) }}</td>
                                        <td class="p-2">{{ number_format($pedido->costo_delivery, 2) }}</td>
                                        <td class="p-1 text-blue-600">
                                            {{ number_format($pedido->total_pago, 2) }}</td>
                                        <td class="p-2 flex justify-center">
                                            @php
                                                $bgColor = match ($pedido->estado) {
                                                    'pendiente' => 'bg-yellow-300',
                                                    'completado' => 'bg-green-300',
                                                    default => 'bg-red-300',
                                                };
                                            @endphp
                                            <span class="px-2 py-1 rounded {{ $bgColor }}">
                                                {{ ucfirst($pedido->estado) }}
                                            </span>
                                        </td>
                                        <td width="10px" class="p-2 text-right">
                                            @if ($pedido->estado === 'pendiente')
                                                <form action="{{ route('pedido.pagar', $pedido->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="bg-blue-500 text-white px-2 py-1 sm:px-4 sm:py-2 rounded hover:bg-blue-700 text-sm sm:text-base">
                                                        Pagar
                                                    </button>
                                                </form>
                                            @else
                                                <div class="flex flex-wrap justify-center gap-2">
                                                    <span class="text-green-600 font-semibold"></span>
                                                    <a href="{{ route('views.pedidos.generarComprobante', $pedido) }}"
                                                        class="text-blue-600">
                                                        <i class="fas fa-file-pdf fa-lg"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Fila oculta con el estado del pedido -->
                                    @if ($pedido->metodo_entrega === 'delivery' && $pedido->estadoPedidos->isNotEmpty())
                                        <tr class="hidden estado-pedido-row" id="estado-pedido-{{ $pedido->id }}">
                                            <td colspan="7">
                                                <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6 mt-2">
                                                    <h4
                                                        class="font-bold text-lg text-gray-700 mb-2 text-center sm:text-left">
                                                        Estado del Pedido</h4>
                                                    <div class="flex flex-wrap justify-center sm:justify-between mt-1">
                                                        @php
                                                            $estados = [
                                                                'En local',
                                                                'En camino',
                                                                'En tu Dirección',
                                                                'Entregado',
                                                            ];
                                                            $estado_actual = $pedido->estadoPedidos->last()->estado;
                                                            $indice_actual = array_search($estado_actual, $estados);
                                                            $estado_fechas = $pedido->estadoPedidos->pluck(
                                                                'created_at',
                                                                'estado',
                                                            );
                                                        @endphp

                                                        @foreach ($estados as $index => $estado)
                                                            <div class="relative flex flex-col items-center w-1/4">
                                                                <div
                                                                    class="w-8 h-8 flex items-center justify-center rounded-full
                                                                    @if ($estado_actual == $estado) bg-green-600 text-white 
                                                                    @elseif($indice_actual > $index) bg-blue-600 text-white 
                                                                    @else bg-gray-300 text-gray-500 @endif">
                                                                    ✔
                                                                </div>
                                                                <span
                                                                    class="mt-2 text-xs sm:text-sm 
                                                                    {{ $estado_actual == $estado ? 'font-bold text-green-600' : 'text-gray-500' }}">
                                                                    {{ $estado }}
                                                                </span>
                                                                <span class="text-xs text-gray-400">
                                                                    @if ($estado_fechas->has($estado))
                                                                        {{ \Carbon\Carbon::parse($estado_fechas[$estado])->format('d/m/Y H:i') }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>


                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".clickable-row").forEach(row => {
                row.addEventListener("click", function() {
                    let pedidoId = this.getAttribute("data-id");
                    let estadoRow = document.getElementById("estado-pedido-" + pedidoId);

                    document.querySelectorAll(".estado-pedido-row").forEach(row => {
                        if (row.id !== "estado-pedido-" + pedidoId) {
                            row.classList.add("hidden");
                        }
                    });

                    estadoRow.classList.toggle("hidden");
                });
            });
        });
    </script>
</x-app-layout>
