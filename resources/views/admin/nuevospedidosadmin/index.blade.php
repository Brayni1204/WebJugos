@extends('adminlte::page')

@section('title', 'Gestión de Pedidos')

@section('content_header')
@stop

@section('content')
    <div class="card">
        <div class="px-4 pt-2">
            <h3 class="card-title">Lista de Pedidos</h3>
        </div>
        <div class="card-header">
            <div>
                <div class="row justify-content-start align-items-center gap-2">
                    <!-- Botón Nuevo Pedido -->
                    <div class="col-12 col-md-auto">
                        <a href="{{ route('admin.nuevospedidosadmin.create') }}" class="btn btn-primary w-100 md:w-auto">
                            <i class="fas fa-plus"></i> Nuevo Pedido
                        </a>
                    </div>

                    <!-- Buscador de Pedidos -->
                    <div class="col-12 col-md-auto">
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control w-100 md:w-auto" id="buscarPedido"
                                placeholder="Buscar por estado..." autocomplete="off">
                            <button class="btn btn-primary d-flex align-items-center" onclick="buscarPedidos()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="text-center">
                        <tr class="bg-gray-200">
                            <th>Codigo</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Mesa / Entrega</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-center" id="tabla-pedidos-body">
                        @foreach ($pedidos as $pedido)
                            <tr>
                                <td>{{ $pedido->id }}</td>
                                <td>{{ $pedido->cliente->nombre ?? 'Datos Reservados' }}</td>
                                <td>{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
                                <td>S/. {{ number_format($pedido->total_pago, 2) }}</td>
                                <td>
                                    <span
                                        class="badge 
                                        @if ($pedido->estado === 'pendiente') bg-warning 
                                        @elseif($pedido->estado === 'completado') bg-success 
                                        @else bg-danger @endif">
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
                                        {{-- ✏ Icono para editar --}}
                                        <a href="{{ route('admin.nuevospedidosadmin.edit', $pedido->id) }}"
                                            class="btn btn-sm"
                                            style="{{ $pedido->estado === 'cancelado' || $pedido->estado === 'completado' ? 'pointer-events: none; background-color: #B0B0B0; opacity: 0.6;' : '' }}">
                                            <i class="fas fa-edit fa-lg" style="color: blue;font-size: 20px"></i>
                                        </a>

                                        <a href="{{ route('admin.nuevospedidosadmin.show', $pedido->id) }}"
                                            class="btn btn-sm">
                                            <i class="fas fa-eye fa-lg" style="color: rgb(255, 123, 0);font-size: 20px"></i>
                                        </a>

                                        <form action="{{ route('admin.nuevospedidosadmin.destroy', $pedido) }}"
                                            method="POST" class="d-inline">
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
                    </tbody>
                </table>
            </div>

        </div>

        <div style="display: flex; justify-content: start; overflow: auto;">
            <div class="pb-6 px-4">
                {{ $pedidos->links('pagination::bootstrap-4') }} {{-- Paginación --}}
            </div>
        </div>
        <div class="floating-btn-container">
            <a href="{{ route('admin.nuevospedidosadmin.create') }}" class="floating-btn" title="Nuevo Pedido">
                <i class="fas fa-plus"></i>
            </a>

            <!-- 🔙 Botón para Regresar -->
            <a href="{{ route('admin.home') }}" class="floating-btn back-btn" title="Regresar">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>



@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <style>
        /* 🎨 Contenedor de Botones */
        .floating-btn-container {
            position: fixed;
            bottom: 2px;
            right: 2px;
            display: grid;
            gap: 12px;
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
    <script>
        function buscarPedidos() {
            let filtro = document.getElementById('buscarPedido').value.trim().toLowerCase();

            if (filtro === '') {
                window.location.href = `{{ route('admin.nuevospedidosadmin.index') }}`;
            } else {
                window.location.href = `{{ route('admin.nuevospedidosadmin.index') }}?estado=${filtro}`;
            }
        }

        function confirmarCompletar(url) {
            Swal.fire({
                title: "Selecciona el método de pago",
                input: "select",
                inputOptions: {
                    "efectivo": "Efectivo",
                    "yape": "Yape",
                    "tarjeta": "Tarjeta",
                    "transferencia": "Transferencia",
                    "plin": "Plin"
                },
                inputPlaceholder: "Elige un método de pago",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Completar pedido",
                cancelButtonText: "Cancelar",
                inputValidator: (value) => {
                    if (!value) {
                        return "Debes seleccionar un método de pago";
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let data = {
                        estado: "completado",
                        metodo_pago: result.value
                    };

                    console.log("Enviando datos:", data); // ✅ Verificar en consola

                    fetch(url, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    "content"),
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify(data)
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log("Respuesta del servidor:", data); // ✅ Verificar en consola
                            if (data.success) {
                                Swal.fire("Completado", "El pedido ha sido completado y convertido en venta.",
                                        "success")
                                    .then(() => {
                                        window.location.href = data.redirect;
                                    });
                            } else {
                                Swal.fire("Error", data.error, "error");
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            Swal.fire("Error", "Hubo un problema al completar el pedido.", "error");
                        });
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    @if (session('success'))
        <script>
            Swal.fire({
                title: "Éxito",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed) {}
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: "Error",
                text: "{{ session('error') }}",
                icon: "error",
                confirmButtonText: "OK"
            });
        </script>
    @endif
    <script>
        function confirmarCancelacion(url) {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Esta acción marcará el pedido como 'Cancelado'.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, cancelar pedido",
                cancelButtonText: "No, volver"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar el formulario por AJAX
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                estado: "cancelado"
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire("Cancelado", "El pedido ha sido cancelado.", "success").then(() => {
                                location.reload(); // Recargar la página
                            });
                        })
                        .catch(error => {
                            Swal.fire("Error", "Hubo un problema al cancelar el pedido.", "error");
                        });
                }
            });
        }
    </script>

    <script>
        function imprimirPedido(pedidoId) {
            let url = `{{ route('admin.nuevospedidosadmin.comprobante', ['id' => '__ID__']) }}`.replace('__ID__',
                pedidoId);

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    let ventanaImpresion = window.open('', '_blank');
                    ventanaImpresion.document.open();
                    ventanaImpresion.document.write(html);
                    ventanaImpresion.document.close();

                    setTimeout(() => {
                        ventanaImpresion.print();
                        ventanaImpresion.close();
                    }, 500);
                })
                .catch(error => {
                    console.error("Error al obtener el comprobante:", error);
                    Swal.fire("Error", "No se pudo cargar el comprobante.", "error");
                });
        }
    </script>

    {{-- ✅ SCRIPT WEBSOCKET MEJORADO PARA ACTUALIZAR SOLO LA TABLA --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Función que pide el nuevo contenido de la tabla y lo reemplaza
            function actualizarContenidoTabla() {
                fetch("{{ route('admin.nuevospedidos.actualizarTabla') }}")
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('tabla-pedidos-body').innerHTML = html;
                    })
                    .catch(error => console.error('❌ Error al actualizar la tabla:', error));
            }

            function connectWebSocket() {
                const socket = new WebSocket("ws://127.0.0.1:8090");

                socket.onopen = () => console.log("🟢 Conexión WebSocket establecida en la página principal.");
                socket.onclose = () => setTimeout(connectWebSocket, 10000); // Intenta reconectar
                socket.onerror = (error) => console.error("🔴 Error de WebSocket:", error);

                socket.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 4000
                        });

                        // Si la acción fue completar o cancelar, recarga la página
                        // porque un pedido podría desaparecer de la lista.
                        if (data.action === 'completado' || data.action === 'cancelado') {
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            // Para cualquier otra acción (nuevo, actualizado), solo refresca la tabla
                            actualizarContenidoTabla();
                        }

                    } catch (e) {
                        console.error("Error al procesar mensaje:", event.data);
                    }
                };
            }
            connectWebSocket();
        });
    </script>

@stop
