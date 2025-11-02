@extends('adminlte::page')

@section('title', 'Pedido Detalle')

@section('content')
    <div class="container mx-auto ">
        <h2 class="text-center font-bold text-2xl text-gray-800">Detalle del Pedido {{ $nuevopedidoadmin->id }}</h2>

        <!-- Dirección de Entrega -->
        @if ($nuevopedidoadmin->direccion)
            <div class="bg-white shadow-lg rounded-lg p-6 mt-2">
                <h4 class="font-bold text-lg text-gray-700 mb-1 border-b pb-2">📍 Dirección de Entrega</h4>
                <div class="grid grid-cols-3 gap-4 text-gray-600">
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold">Departamento:</span>
                        <span>{{ $nuevopedidoadmin->direccion->departamento }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold">Provincia:</span>
                        <span>{{ $nuevopedidoadmin->direccion->provincia }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold">Distrito:</span>
                        <span>{{ $nuevopedidoadmin->direccion->distrito }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold">Calle:</span>
                        <span>{{ $nuevopedidoadmin->direccion->calle }}</span>
                    </div>
                    <div class="col-span-2 flex items-center space-x-2">
                        <span class="font-semibold">N°:</span>
                        <span>{{ $nuevopedidoadmin->direccion->numero }}</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Timeline del Estado del Pedido -->
        @if ($nuevopedidoadmin->estadoPedidos->isNotEmpty())
            <div class="bg-white shadow-lg rounded-lg p-6 mt-2">
                <h4 class="font-bold text-lg text-gray-700 mb-2">Estado del Pedido</h4>
                <div id="estadoPedidoContainer" class="flex items-center justify-between relative mt-1">
                    @php
                        $estados = ['En local', 'En camino', 'En tu Dirección', 'Entregado'];
                        $estado_actual = $nuevopedidoadmin->estadoPedidos->last()->estado;
                        $indice_actual = array_search($estado_actual, $estados);
                        $estado_fechas = $nuevopedidoadmin->estadoPedidos->pluck('created_at', 'estado');
                    @endphp

                    @foreach ($estados as $index => $estado)
                        <div class="relative flex flex-col items-center">
                            <div id="estado_{{ $index }}"
                                class="w-8 h-8 flex items-center justify-center rounded-full
                            @if ($estado_actual == $estado) bg-green-600 text-white 
                            @elseif($indice_actual > $index) bg-blue-600 text-white 
                            @else bg-gray-300 text-gray-500 @endif">
                                ✔
                            </div>
                            <span id="texto_{{ $index }}"
                                class="mt-2 text-sm 
                            {{ $estado_actual == $estado ? 'font-bold text-green-600' : 'text-gray-500' }}">
                                {{ $estado }}
                            </span>
                            <span id="fecha_{{ $index }}" class="text-xs text-gray-400">
                                @if ($estado_fechas->has($estado))
                                    {{ \Carbon\Carbon::parse($estado_fechas[$estado])->format('d/m/Y H:i') }}
                                @endif
                            </span>
                        </div>

                        @if ($index < count($estados) - 1)
                            <div id="linea_{{ $index }}"
                                class="flex-1 h-1 mx-2 
                            @if ($indice_actual > $index) bg-blue-600 
                            @else bg-gray-300 @endif">
                            </div>
                        @endif
                    @endforeach
                </div>

                <div>
                    @if ($nuevopedidoadmin->estadoPedidos->last()->estado != 'Entregado')
                        <button id="actualizarEstadoBtn" class="btn btn-primary mt-4">Actualizar Estado</button>
                    @endif
                </div>
            </div>
        @endif

        <script>
            document.getElementById("actualizarEstadoBtn")?.addEventListener("click", function() {
                let pedidoId = {{ $nuevopedidoadmin->id }};

                fetch("{{ route('pedidos.cambiarEstado', $nuevopedidoadmin->id) }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let estados = ['En local', 'En camino', 'En tu Direccion', 'Entregado'];
                            let nuevoEstado = data.nuevo_estado;
                            let indiceActual = estados.indexOf(nuevoEstado);

                            estados.forEach((estado, index) => {
                                let estadoElement = document.getElementById(`estado_${index}`);
                                let textoElement = document.getElementById(`texto_${index}`);
                                let fechaElement = document.getElementById(`fecha_${index}`);
                                let lineaElement = document.getElementById(`linea_${index}`);

                                if (estadoElement) {
                                    if (index < indiceActual) {
                                        estadoElement.className =
                                            "w-8 h-8 flex items-center justify-center rounded-full bg-blue-600 text-white";
                                    } else if (index === indiceActual) {
                                        estadoElement.className =
                                            "w-8 h-8 flex items-center justify-center rounded-full bg-green-600 text-white";
                                    } else {
                                        estadoElement.className =
                                            "w-8 h-8 flex items-center justify-center rounded-full bg-gray-300 text-gray-500";
                                    }
                                }

                                if (textoElement) {
                                    textoElement.className = index === indiceActual ?
                                        "mt-2 text-sm font-bold text-green-600" :
                                        "mt-2 text-sm text-gray-500";
                                }

                                if (fechaElement && index === indiceActual) {
                                    fechaElement.innerText = data.fecha;
                                }

                                if (lineaElement) {
                                    lineaElement.className = index < indiceActual ?
                                        "flex-1 h-1 mx-2 bg-blue-600" : "flex-1 h-1 mx-2 bg-gray-300";
                                }
                            });

                            // Ocultar botón si el estado es "Entregado"
                            if (nuevoEstado === "Entregado") {
                                document.getElementById("actualizarEstadoBtn").style.display = "none";
                            }
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error("Error:", error));
            });
        </script>


        <!-- Detalles del Pedido -->
        <div class="bg-white shadow-lg rounded-lg p-6 mt-2">
            <h4 class="font-bold text-lg text-gray-700 mb-2">Detalles del Pedido</h4>
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300 mt-2">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border p-2">Producto</th>
                            <th class="border p-2">Cantidad</th>
                            <th class="border p-2">Precio Unitario</th>
                            <th class="border p-2">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($nuevopedidoadmin->detalles as $detalle)
                            <tr class="text-center">
                                <td class="border p-2">{{ $detalle->producto->nombre_producto ?? 'Producto Eliminado' }}
                                </td>
                                <td class="border p-2">{{ $detalle->cantidad }}</td>
                                <td class="border p-2">S/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td class="border p-2 font-semibold">S/. {{ number_format($detalle->precio_total, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-2">
            <!-- Información del Pedido -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h4 class="font-bold text-lg text-gray-700 mb-2">Información del Pedido</h4>
                <p><strong>Fecha:</strong> {{ $nuevopedidoadmin->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Estado:</strong> <span class="text-blue-600">{{ ucfirst($nuevopedidoadmin->estado) }}</span></p>
                <p><strong>Método de Entrega:</strong> {{ ucfirst($nuevopedidoadmin->metodo_entrega) }}</p>
                <p><strong>Método de Pago:</strong> {{ ucfirst($nuevopedidoadmin->metodo_pago ?? 'N/A') }}</p>
                <p><strong>Total Pagado:</strong> <span class="text-green-600 font-semibold">S/.
                        {{ number_format($nuevopedidoadmin->total_pago, 2) }}</span></p>
            </div>

            <!-- Información del Cliente -->
            @if ($nuevopedidoadmin->cliente)
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h4 class="font-bold text-lg text-gray-700 mb-2">Cliente</h4>
                    <p><strong>Nombre:</strong> {{ $nuevopedidoadmin->cliente->nombre }}</p>
                    <p><strong>Email:</strong> {{ $nuevopedidoadmin->cliente->email }}</p>
                    <p><strong>Teléfono:</strong> {{ $nuevopedidoadmin->cliente->telefono }}</p>
                </div>
            @endif

            <!-- Información de la Mesa -->
            @if ($nuevopedidoadmin->mesa)
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h4 class="font-bold text-lg text-gray-700 mb-2">Mesa</h4>
                    <p><strong>Número de Mesa:</strong> {{ $nuevopedidoadmin->mesa->numero_mesa }}</p>
                    <p><strong>Estado:</strong> {{ ucfirst($nuevopedidoadmin->mesa->estado) }}</p>
                </div>
            @endif
        </div>


    </div>
    <div class="floating-btn-container">
        <!-- 🔹 Botón para Agregar Subtítulo -->
        @if ($nuevopedidoadmin->estado === 'pendiente')
            <!-- 🔹 Botón para Editar (Solo si el estado es Pendiente) -->
            <a href="{{ route('admin.nuevospedidosadmin.edit', $nuevopedidoadmin->id) }}" class="floating-btn"
                title="Ir a Editar">
                <i class="fas fa-edit"></i>
            </a>
        @endif

        <!-- 🔙 Botón para Regresar -->
        <a href="{{ route('admin.nuevospedidosadmin.index') }}" class="floating-btn back-btn" title="Regresar">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>

@stop

@section('css')
    <style>
        .floating-btn-container {
            position: fixed;
            bottom: 10px;
            right: 0px;
            display: grid;
            gap: 10px;
            align-items: center;
        }

        /* 🎨 Estilo General de Botones Flotantes */
        .floating-btn {
            background-color: #007bff;
            color: white;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 22px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .floating-btn:hover {
            background-color: #0056b3;
            transform: scale(1.1);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.4);
        }

        /* 🟥 Botón de Regresar */
        .back-btn {
            background-color: #dc3545;
        }

        .back-btn:hover {
            background-color: #b02a37;
        }

        /* 🟩 Botón para Publicar/Borrador */
        .status-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
        }

        .draft-btn {
            background-color: #f39c12;
        }

        .draft-btn:hover {
            background-color: #d68910;
        }

        .publish-btn {
            background-color: #28a745;
        }

        .publish-btn:hover {
            background-color: #218838;
        }

        /* 👁️ Botón de Ver */
        .view-btn {
            background-color: #17a2b8;
        }

        .view-btn:hover {
            background-color: #138496;
        }

        /* 🎯 Estilo para el Formulario Flotante */
        .floating-btn-form {
            display: inline-block;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.tailwindcss.com"></script>
@stop
