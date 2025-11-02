@extends('adminlte::page')

@section('title', 'Editar Pedido')

@section('content_header')
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="container-fluid">
                <div class="container mx-auto px-4 py-8">
                    <div class="p-11">
                        {{-- <form action="" method="post"> --}}
                        <div>
                            <div class="row">
                                <div class="col-md-8 border border-dark p-3">
                                    <div class="d-flex align-items-center flex-wrap gap-3 p-3 bg-light rounded shadow-sm">
                                        <div class="flex-grow-1" style="margin-right: 5px">
                                            <label for="categoria" class="fw-bold text-muted">Categoría:</label>
                                            <select id="categoria"
                                                class="form-control border border-secondary rounded-lg px-3 py-2 shadow-sm">
                                                <option value="">Todas</option>
                                                @foreach ($categoriasventa as $item)
                                                    <option value="{{ $item->id }}">{{ $item->nombre_categoria }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label for="buscar_producto" class="fw-bold text-muted">Buscar
                                                Producto:</label>
                                            <input id="buscar_producto" type="text"
                                                class="form-control border border-secondary rounded-lg px-3 py-2 shadow-sm"
                                                placeholder="Buscar por nombre, número o precio">
                                        </div>
                                    </div>

                                    <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                                        <table class="table table-bordered">
                                            <thead class="text-center">
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Precio</th>
                                                </tr>
                                            </thead>
                                            <tbody id="lista-productos" class="text-center">
                                                @foreach ($productosventa as $producto)
                                                    <tr data-id="{{ $producto->id }}"
                                                        data-nombre="{{ $producto->nombre_producto }}"
                                                        data-precio="{{ $producto->precios->precio_venta ?? '10' }}"
                                                        data-imagen="{{ asset('storage/' . $producto->image->first()->url) }}"
                                                        data-categoria="{{ $producto->id_categoria }}">
                                                        <!-- Aquí se añade el data-categoria -->
                                                        <td>{{ $producto->nombre_producto }}</td>
                                                        <td>{{ $producto->precios->precio_venta ?? '10' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- Sección de detalles del producto -->
                                <div class="col-md-4 border border-dark p-3">
                                    <h5>Detalle del Producto</h5>
                                    <div id="detalle-producto" class="border p-3 bg-light flex justify-center text-center"
                                        style="height: 300px; overflow-y: auto;">
                                        <p class="text-muted">Seleccione un producto para ver los detalles aquí.</p>
                                    </div>
                                    <div style="display: flex; justify-content: center; margin-top: 5px">
                                        <button type="button" class="btn btn-primary w-max" id="agregar-producto"
                                            style="display: none; width: 100%" onclick="agregarProducto()">Agregar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="caracteristicas-container">
                            <label><input type="checkbox" name="caracteristicas[]" class="grupo-temperatura" value="helado">
                                Helado</label>
                            <label><input type="checkbox" name="caracteristicas[]" class="grupo-temperatura"
                                    value="temperado"> Temperado</label>
                            <label><input type="checkbox" name="caracteristicas[]" class="grupo-temperatura"
                                    value="temperatura ambiente"> Temperatura ambiente</label>

                            <label><input type="checkbox" name="caracteristicas[]" class="grupo-azucar" value="con azúcar">
                                Con azúcar</label>
                            <label><input type="checkbox" name="caracteristicas[]" class="grupo-azucar" value="sin azúcar">
                                Sin azúcar</label>
                            <label><input type="checkbox" name="caracteristicas[]" class="grupo-azucar"
                                    value="bajo en azúcar"> Bajo en azúcar</label>
                        </div>

                        <div class="flex flex-column">
                            <div style="margin-top: 20px">
                                <h5>Productos Agregados</h5>
                            </div>
                            <div style="overflow: auto;">
                                <table class="table table-bordered">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio</th>
                                            <th>Total</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productosLista" class="text-center">
                                        @foreach ($nuevopedidoadmin->detalles as $detalle)
                                            <tr data-id="{{ $detalle->id }}">
                                                <td>{{ $detalle->descripcion }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button"
                                                            onclick="modificarCantidad('decrementar', {{ $detalle->id }})">➖</button>
                                                        <span id="cantidad-{{ $detalle->id }}" class="px-2"
                                                            style="font-size: 25px">{{ $detalle->cantidad }}</span>
                                                        <button type="button"
                                                            onclick="modificarCantidad('incrementar', {{ $detalle->id }})">➕</button>
                                                    </div>
                                                </td>
                                                <td>S/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                                                <td id="subtotal-{{ $detalle->id }}">S/.
                                                    {{ number_format($detalle->precio_total, 2) }}</td>
                                                <td>
                                                    <button class="btn btn-danger btn-sm eliminar-producto"
                                                        onclick="eliminarProducto({{ $detalle->id }})">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>


                                </table>
                            </div>

                            <div>
                                <div class="mb-3 text-end">
                                    <h3>Total: S/. <span id="TotalPedido">{{ $nuevopedidoadmin->subtotal }}</span></h3>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="mt-6 p-1 bg-white shadow-md rounded-lg">
                                <h2 class="text-lg font-bold text-gray-800">Información del Cliente</h2>

                                <form action="{{ route('admin.nuevospedidosadmin.update', $nuevopedidoadmin->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="pedido_id" value="{{ $nuevopedidoadmin->id }}">
                                    <div style="display: ruby;">
                                        <div class="flex flex-wrap md:flex-nowrap gap-4">
                                            <div class="w-full md:w-1/2">
                                                <label for="nombre" class="text-gray-500 text-lg">Nombre:</label>
                                                <input type="text" name="nombre" id="nombre"
                                                    class="w-full p-2 border rounded"
                                                    value="{{ $nuevopedidoadmin->cliente->nombre ?? '' }}"
                                                    autocomplete="off">

                                                <label for="email" class="text-gray-500 text-lg">Correo:</label>
                                                <input type="email" name="email" id="email"
                                                    class="w-full p-2 border rounded"
                                                    value="{{ $nuevopedidoadmin->cliente->email ?? '' }}"
                                                    autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="text-center px-4 pt-1"
                                            style="display: flex; height: 100%; justify-content: center">
                                            <button type="submit" class="btn btn-primary">Actualizar Cliente</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Método de Entrega -->
                        <div class="mb-3">
                            <label for="metodo_entrega" class="form-label">Método de Entrega</label>
                            <select name="metodo_entrega" id="metodo_entrega" class="form-control">
                                <option value="mesa">Mesa</option>
                            </select>
                        </div>

                        <div class="floating-btn-container">
                            <button type="button" class="floating-btn" title="Realizar venta"
                                onclick="procesarPedido('{{ route('admin.pedidos.completar', $nuevopedidoadmin) }}', obtenerTotalPedido(), {{ $nuevopedidoadmin->id }})"
                                style="{{ $nuevopedidoadmin->estado === 'cancelado' || $nuevopedidoadmin->estado === 'completado' ? 'pointer-events: none; background-color: #B0B0B0; opacity: 0.6;' : '' }}"
                                {{ $nuevopedidoadmin->estado === 'cancelado' || $nuevopedidoadmin->estado === 'completado' ? 'disabled' : '' }}>
                                <i class="fas fa-save"></i>
                            </button>


                            <a href="{{ route('admin.nuevospedidosadmin.index') }}" class="floating-btn back-btn"
                                title="Regresar">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@stop

@section('js')
    <script>
        function procesarPedido(url, totalPedido, pedidoId) {
            Swal.fire({
                customClass: {
                    confirmButton: "btn-confirm",
                    cancelButton: "btn-cancel"
                },
                title: "Selecciona el método de pago",
                html: `<h3>Total: S/. ${totalPedido.toFixed(2)}</h3>`,
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
                confirmButtonText: "Siguiente",
                cancelButtonText: "Cancelar",
                inputValidator: (value) => {
                    if (!value) {
                        return "Debes seleccionar un método de pago";
                    }
                }

            }).then((result) => {
                if (result.isConfirmed) {
                    let metodoPago = result.value;

                    if (metodoPago === "efectivo") {
                        solicitarMontoEfectivo(url, totalPedido, pedidoId, metodoPago);
                    } else {
                        completarPedido(url, pedidoId, metodoPago, null, null);
                    }
                }
            });
        }

        function solicitarMontoEfectivo(url, totalPedido, pedidoId, metodoPago) {
            Swal.fire({
                customClass: {
                    confirmButton: "btn-confirm",
                    cancelButton: "btn-cancel"
                },
                title: "Pago en efectivo",
                html: `<h3>Total: S/. ${totalPedido.toFixed(2)}</h3>
                <label>Monto recibido:</label>
                <div style="display: grid;">
                    <input type="number" id="monto_recibido" class="swal2-input m-0" min="${totalPedido}">
                    <label class="m-0 p-2">Vuelto:</label>
                    <input type="text" id="vuelto" class="swal2-input m-0" disabled>
                </div>`,
                showCancelButton: true,
                confirmButtonText: "Completar pedido",
                cancelButtonText: "Cancelar",
                didOpen: () => {
                    let montoRecibidoInput = document.getElementById("monto_recibido");
                    let vueltoInput = document.getElementById("vuelto");

                    montoRecibidoInput.focus();

                    montoRecibidoInput.addEventListener("input", function() {
                        let montoRecibido = parseFloat(montoRecibidoInput.value) || 0;
                        let vuelto = montoRecibido - totalPedido;
                        vueltoInput.value = vuelto >= 0 ? vuelto.toFixed(2) : "0.00";
                    });

                    // Inicializa el vuelto correctamente
                    montoRecibidoInput.dispatchEvent(new Event("input"));
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let montoRecibido = parseFloat(document.getElementById("monto_recibido").value);
                    let vuelto = parseFloat(document.getElementById("vuelto").value);

                    if (montoRecibido >= totalPedido) {
                        completarPedido(url, pedidoId, metodoPago, montoRecibido, vuelto);
                    } else {
                        Swal.fire("Error", "El monto recibido no puede ser menor al total.", "error");
                    }
                }
            });
        }

        function completarPedido(url, pedidoId, metodoPago, montoRecibido, vuelto) {
            let data = {
                estado: "completado",
                metodo_pago: metodoPago
            };

            if (metodoPago === "efectivo") {
                data.monto_recibido = montoRecibido;
                data.vuelto = vuelto;
            }

            fetch(url, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        imprimirPedido(pedidoId, data.redirect);
                    } else {
                        Swal.fire("Error", data.error, "error");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire("Error", "Hubo un problema al completar el pedido.", "error");
                });
        }

        function imprimirPedido(pedidoId, redirectUrl) {
            let url = `{{ route('admin.nuevospedidosadmin.comprobantedetalle', ['id' => '__ID__']) }}`.replace('__ID__',
                pedidoId);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.html) {
                        let ventanaImpresion = window.open('', '');
                        ventanaImpresion.document.open();
                        ventanaImpresion.document.write(data.html);
                        ventanaImpresion.document.close();

                        setTimeout(() => {
                            ventanaImpresion.print();
                            ventanaImpresion.close();
                            window.location.href = redirectUrl;
                        }, 500);
                    } else {
                        Swal.fire("Error", "No se pudo generar el comprobante.", "error");
                    }
                })
                .catch(error => {
                    console.error("Error en la solicitud AJAX:", error);
                    Swal.fire("Error", "Hubo un error en la conexión con el servidor.", "error");
                });
        }

        function obtenerTotalPedido() {
            let totalElement = document.querySelector("#TotalPedido");
            return totalElement ? parseFloat(totalElement.innerText) : 0;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function filtrarProductos() {
            const categoriaSeleccionada = document.querySelector('#categoria').value.toLowerCase();
            const busqueda = document.querySelector('#buscar_producto').value.toLowerCase();
            const filas = document.querySelectorAll('#lista-productos tr');

            filas.forEach(fila => {
                const nombreProducto = fila.dataset.nombre.toLowerCase();
                const categoriaProducto = fila.dataset.categoria;
                const precioProducto = fila.dataset.precio.toLowerCase();
                const mostrarPorCategoria = categoriaSeleccionada === '' || categoriaProducto ===
                    categoriaSeleccionada;
                const mostrarPorBusqueda = nombreProducto.includes(busqueda) || precioProducto.includes(busqueda);

                if (mostrarPorCategoria && mostrarPorBusqueda) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        }
        document.querySelector('#categoria').addEventListener('change', filtrarProductos);
        document.querySelector('#buscar_producto').addEventListener('input', filtrarProductos);
        document.querySelectorAll('#lista-productos tr').forEach(row => {
            row.addEventListener('click', function() {
                const id = this.dataset.id;
                const nombre = this.dataset.nombre;
                const precio = parseFloat(this.dataset.precio);
                const imagen = this.dataset.imagen;

                // Mostrar los detalles
                document.querySelector('#detalle-producto').innerHTML = `
                    <p><strong>Nombre:</strong> ${nombre}</p>
                    <p><strong>Precio:</strong> $${precio.toFixed(2)}</p>
                    ${imagen ? `<img src="${imagen}" alt="Imagen de ${nombre}" style="max-width: 40%; height: auto;">` : '<p class="text-muted">Sin imagen disponible</p>'}
                `;

                productoSeleccionado = {
                    id,
                    nombre,
                    precio,
                    cantidad: 1
                };

                document.querySelector('#agregar-producto').style.display = 'block';
            });
        });
    </script>

    <script>
        function actualizarTablaProductos() {
            fetch("{{ route('admin.nuevospedidosdetalleadmin.detalles', $nuevopedidoadmin->id) }}")
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log("🔄 Actualizando tabla con nuevos datos...");
                        document.querySelector("#productosLista").innerHTML = data.html;
                        document.querySelector("#TotalPedido").innerText = `${data.total}`; // 🔥 Actualizar total
                    } else {
                        console.error("Error en el servidor:", data.error, "en", data.file, "línea", data.line);
                    }
                })
                .catch(error => {
                    console.error("❌ Error al actualizar la tabla:", error);
                });
        }

        // **MODIFICAR CANTIDAD (➖ o ➕)**
        function modificarCantidad(accion, detalleId) {
            console.log("✅ detalleId recibido en modificarCantidad():", detalleId);
            if (!detalleId || detalleId === 0) {
                console.error("❌ Error: detalleId no válido", detalleId);
                return;
            }

            fetch(`{{ route('admin.nuevospedidosdetalleadmin.update', ':id') }}`.replace(':id', detalleId), {
                    method: "PUT",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        accion: accion
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        actualizarTablaProductos();
                    } else {
                        Swal.fire({
                            icon: "warning",
                            title: "No permitido",
                            text: data.message,
                            confirmButtonText: "Entendido",
                        });
                    }
                })
                .catch(error => {
                    console.error("Error al actualizar la cantidad:", error);
                });
        }

        // **AGREGAR PRODUCTO**
        function agregarProducto() {
            if (!productoSeleccionado) {
                alert("Seleccione un producto primero.");
                return;
            }

            let caracteristicasSeleccionadas = Array.from(document.querySelectorAll(
                    'input[name="caracteristicas[]"]:checked'))
                .map(cb => cb.value);

            const data = {
                pedido_id: "{{ $nuevopedidoadmin->id }}",
                producto_id: productoSeleccionado.id,
                cantidad: 1,
                caracteristicas: caracteristicasSeleccionadas,
                precio_unitario: productoSeleccionado.precio
            };

            fetch("{{ route('admin.nuevospedidosdetalleadmin.store') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    console.log("✅ Producto agregado:", data);
                    actualizarTablaProductos(); // Actualizar la tabla sin recargar
                })
                .catch(error => {
                    console.error("❌ Error al enviar el producto:", error);
                });
        }

        // **ELIMINAR PRODUCTO**
        function eliminarProducto(detalleId) {
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Este producto será eliminado del pedido.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ route('admin.nuevospedidosdetalleadmin.destroy', ':id') }}`.replace(':id',
                            detalleId), {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Content-Type": "application/json"
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Eliminado",
                                    text: data.message,
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    actualizarTablaProductos(); // Actualizar la tabla sin recargar
                                });
                            }
                        })
                        .catch(error => {
                            console.error("Error al eliminar el producto:", error);
                        });
                }
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function aplicarExclusividad(grupoClase) {
                const checkboxes = document.querySelectorAll(`.${grupoClase}`);
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', () => {
                        if (checkbox.checked) {
                            checkboxes.forEach(cb => {
                                if (cb !== checkbox) cb.checked = false;
                            });
                        }
                    });
                });
            }

            aplicarExclusividad('grupo-temperatura');
            aplicarExclusividad('grupo-azucar');
        });
    </script>
    {{-- ✅ AÑADE ESTE NUEVO SCRIPT AL FINAL --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function connectWebSocket() {
                const socket = new WebSocket("ws://127.0.0.1:8090");

                socket.onopen = () => console.log("🟢 Conexión WebSocket establecida en la página de EDICIÓN.");
                socket.onclose = () => setTimeout(connectWebSocket, 3000);
                socket.onerror = (error) => console.error("🔴 Error de WebSocket:", error);

                socket.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        console.log("📩 Notificación recibida en EDIT:", data);

                        // Muestra una notificación bonita
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: data.message, // Usa el mensaje del servidor
                            showConfirmButton: false,
                            timer: 3000
                        });

                        // ✅ Llama a la función que ya tienes para refrescar la tabla de productos
                        actualizarTablaProductos();

                    } catch (e) {
                        console.error("Error al procesar mensaje:", event.data, e);
                    }
                };
            }
            connectWebSocket();
        });
    </script>
@stop

@section('css')
    <style>
        .caracteristicas-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 1rem;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .caracteristicas-container label {
            background-color: #ffffff;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 8px 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Segoe UI', sans-serif;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .caracteristicas-container input[type="checkbox"] {
            accent-color: #28a745;
            /* Color verde moderno */
            transform: scale(1.2);
        }

        .caracteristicas-container label:hover {
            background-color: #e6f5ea;
            border-color: #28a745;
        }


        .btn-confirm {
            background-color: #007bff !important;
            color: white !important;
            border: none !important;
            padding: 10px 20px;
            font-size: 16px;
        }

        .btn-cancel {
            background-color: #dc3545 !important;
            color: white !important;
            border: none !important;
            padding: 10px 20px;
            font-size: 16px;
        }

        .swal2-confirm:focus,
        .swal2-cancel:focus {
            box-shadow: none !important;
        }
    </style>

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

        /* 🎨 Contenedor de Botones */
        .floating-btn-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
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
