<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $empresa = \App\Models\Empresa::latest()->first();
        $favicon =
            $empresa && $empresa->favicon_url
                ? asset('storage/' . $empresa->favicon_url)
                : asset('default-favicon.ico');
    @endphp

    <title>Detalle del Pedido</title>
    <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            padding: 0;
            margin: 0;
            object-fit: cover;
        }

        .aside {
            position: fixed;
            width: 25%;
            right: 0;
            display: flex;
            flex-direction: column;
            height: 97vh;
            padding: 10px;
            background: #f9f9f9;
            border-left: 1px solid #ddd;
        }

        .contenedor--pedido {
            width: 75%;
        }

        #toggleAside {
            display: none;
        }

        /* Estilos para centrar el modal */
        #my-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 95%;
            max-width: 400px;
            /* Ajuste del tamaño máximo */
            background: white;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
            padding: 10px;
            border-radius: 10px;
            z-index: 1001;
            display: none;
            /* Oculto por defecto */
        }

        /* Estilos para la animación de apertura */
        .modal-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translate(-50%, -55%);
            }

            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }


        @media (max-width: 500px) {
            .contenedor {
                flex-direction: column;
                align-items: center;
            }

            .contenedor--pedido {
                width: 100%;
            }

            .aside {
                display: none;
            }

            #toggleAside {
                display: flex;
            }

        }
    </style>
</head>

<body>
    <div style="width: 100%">
        <div>
            <div class="contenedor" style="display: flex; width: 100%;">
                <div class="contenedor--pedido">
                    <div style="padding: 20px">
                        <div style="padding: 10px">
                            <div style="display: flex; justify-content: center">
                                <h1>Pedidos Mesa N° - {{ $mesa->id }}</h1>
                            </div>
                        </div>
                        <div>
                            <div style="display: flex; justify-content: center; padding-bottom: 20px">
                                <h2>Jugos en Tu Pedido</h2>
                            </div>
                            <div id="lista-productos-cliente"
                                style="width: 100%; display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: center;">
                                @foreach ($pedido->detalles as $detalle)
                                    <div
                                        style="border-radius: 0.5rem; background-color: #f3f3f3; padding: 8px; width: 6rem; display: flex; flex-direction: column; align-items: center; box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.1);">
                                        <!-- Imagen del producto -->
                                        <div
                                            style="width: 5rem; height: 5rem; overflow: hidden; border-radius: 0.5rem;">
                                            @if ($detalle->producto->image->isNotEmpty())
                                                <img src="{{ asset('storage/' . $detalle->producto->image->first()->url) }}"
                                                    alt="{{ $detalle->nombre_producto }}"
                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <div
                                                    style="width: 100%; height: 100%; background-color: #ccc; display: flex; align-items: center; justify-content: center;">
                                                    <span style="color: #666; font-size: 10px;">Sin Imagen</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Información del producto -->
                                        <div style="text-align: center; font-size: 12px; margin-top: 4px;">
                                            <strong
                                                style="display: block; font-size: 16px;">{{ $detalle->nombre_producto }}</strong>
                                            <p style="margin: 2px 0; font-size: 13px;">Cantidad:
                                                <strong>{{ $detalle->cantidad }}</strong>
                                            </p>
                                            <p style="margin: 2px 0; font-size: 16px; color: #702727;">
                                                S/{{ number_format($detalle->precio_total, 2) }}</p>
                                        </div>

                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>

                    <div style="padding: 20px">
                        <div>
                            <!-- Título de la sección -->
                            <div>
                                <div class="flex flex-col items-center my-4 gap-4">
                                    {{-- Buscador y botón --}}
                                    <div class="flex w-full justify-center gap-2">
                                        <input type="text" id="buscador" placeholder="Buscar jugo..."
                                            class="w-1/2 p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                        <button id="toggleCategorias"
                                            class="p-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-md transition">🧃
                                            Categorías</button>
                                    </div>

                                    {{-- Lista de Categorías --}}
                                    <div id="categoriaList"
                                        class="hidden flex flex-wrap gap-3 justify-center px-4 py-3 rounded-md bg-gray-100 border border-gray-300 shadow-inner w-full max-w-4xl">
                                        @php
                                            $categoriaActiva = request()->get('categoria');
                                        @endphp

                                        <a href="{{ route('pedido.ver', ['mesa' => request('mesa'), 'buscar' => request('buscar')]) }}"
                                            class="px-4 py-2 rounded-full text-sm font-medium transition {{ !$categoriaActiva ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                                            Todos
                                        </a>

                                        @foreach ($categorias as $item)
                                            <a href="{{ route('pedido.ver', ['mesa' => request('mesa'), 'categoria' => $item->id, 'buscar' => request('buscar')]) }}"
                                                class="px-4 py-2 rounded-full text-sm font-medium transition {{ $categoriaActiva == $item->id ? 'bg-blue-600 text-white shadow-md' : 'bg-blue-100 text-blue-800 hover:bg-blue-300' }}">
                                                {{ $item->nombre_categoria }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>


                            <!-- Contenedor de productos -->
                            <div id="listaProductos"
                                class="w-full grid gap-4 justify-center grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 px-4">

                                @foreach ($productos as $producto)
                                    @php
                                        // 🚀 CÓDIGO CORREGIDO para generar la URL de la imagen de forma segura
                                        // Esto previene el "Attempt to read property 'url' on null"
                                        $imageUrl = '';
                                        if ($producto->image->isNotEmpty()) {
                                            $imageUrl = asset('storage/' . ($producto->image->first()->url ?? ''));
                                        }
                                    @endphp
                                    <button {{-- Usamos la variable $imageUrl precalculada --}}
                                        onclick="agregarAlCarrito({{ $producto->id }}, '{{ $producto->nombre_producto }}', {{ $producto->precios->precio_venta ?? 0 }}, '{{ $imageUrl }}')"
                                        class="producto bg-white border border-gray-300 rounded-lg p-4 shadow-md hover:shadow-xl hover:scale-105 transition transform duration-300 ease-in-out flex flex-col items-center"
                                        data-nombre="{{ strtolower($producto->nombre_producto) }}">
                                        <div class="w-full
            aspect-[4/5] overflow-hidden rounded-md">
                                            @if ($producto->image->isNotEmpty())
                                                <img src="{{ asset('storage/' . $producto->image->first()->url) }}"
                                                    alt="{{ $producto->nombre_producto }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div
                                                    style="width: 100%; height: 100%; background-color: #ccc; display: flex; align-items: center; justify-content: center;">
                                                    <span style="color: #666;">Imagen No Disponible</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div style="text-align: center; margin-top: 8px;">
                                            <strong>{{ $producto->nombre_producto }}</strong>
                                            <p style="margin-top: 5px;">Precio:
                                                S/{{ number_format($producto->precios->precio_venta ?? 0, 2) }}</p>
                                        </div>

                                    </button>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
                <aside class="aside">
                    <div>

                    </div>
                    <!-- Título -->
                    <div style="text-align: center; margin-bottom: 10px;">
                        <h2>Lista para Acatualizar Pedido</h2>
                    </div>

                    <!-- Contenedor de Productos -->
                    <div id="carrito" style="flex-grow: 1; overflow-y: auto; padding-bottom: 10px;">
                        <div id="carritoLista" class="space-y-2" style="height: 525px; overflow: auto;">
                            {{-- Productos agregados dinámicamente aquí --}}
                        </div>
                        <div class="flex justify-between">
                            <p class="text-lg font-semibold text-gray-700 mt-4">Subtotal: S/ <span class="text-blue-600"
                                    id="totalPedido">0.00</span></p>
                            <p class="flex items-end">Total: S/ <span id="totalDetallePagar"
                                    class="text-blue-600">0.00</span></p>
                        </div>
                    </div>

                    <!-- Totales -->
                    <div style="padding: 10px; border-top: 1px solid #ddd; text-align: center; background: #fff;">

                    </div>

                    <!-- Botón Actualizar Pedido -->
                    <button type="button" onclick="actualizarPedido()"
                        style="width: 100%; background: #007bff; color: white; padding: 10px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin-top: 10px;">
                        🔄 Actualizar Pedido
                    </button>


                    <form action="{{ route('pedidoMesa.pagar', $pedido->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            style="width: 100%; padding: 10px; background: #135287; color: white; border: none; 
                            border-radius: 5px; cursor: pointer; margin-top: 10px;">
                            Pagar S/ <span id="totalDetalle">0.00</span>
                        </button>
                    </form>
                    <!-- Botón Pagar -->
                </aside>
            </div>
        </div>

        <!-- Botón flotante para mostrar el carrito en móviles -->
        <button id="toggleAside"
            style="position: fixed; bottom: 20px; right: 20px; background: #135287; color: white; border: none; padding: 12px 16px; border-radius: 50%; font-size: 16px; cursor: pointer; z-index: 1000;">
            🛒 <span id="contadorProductos">0</span>
        </button>

        <script>
            document.addEventListener("DOMContentLoaded", () => {
                actualizarCarrito();
                actualizarContador();
            });

            let carrito = [];
            let totalDetalle = {{ $pedido->detalles->sum('precio_total') }}; // Suma de los productos ya agregados en el pedido
            document.getElementById("totalDetalle").innerText = totalDetalle.toFixed(2);

            function agregarAlCarrito(id, nombre, precio, imagen) {
                let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

                let productoEnCarrito = carrito.find(producto => producto.id === id);

                if (productoEnCarrito) {
                    productoEnCarrito.cantidad += 1;
                } else {
                    carrito.push({
                        id,
                        nombre,
                        precio,
                        cantidad: 1,
                        imagen: imagen || null
                    });
                }

                localStorage.setItem("carrito", JSON.stringify(carrito)); // Guardar cambios en localStorage
                actualizarCarrito();
                actualizarContador();
                actualizarModal();
            }


            function actualizarCarrito() {
                let carritoLista = document.getElementById("carritoLista");
                let totalPedido = document.getElementById("totalPedido");
                let totalDetallePagar = document.getElementById("totalDetallePagar");
                let totalDetalleModal = document.getElementById("totalDetalleModal");


                let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
                carritoLista.innerHTML = "";
                // 🚨 Verifica si los elementos existen antes de intentar modificar sus valores
                if (!carritoLista || !totalPedido || !totalDetallePagar || !totalDetalleModal) {
                    console.error("Uno o más elementos del carrito no existen en el DOM.");
                    return;
                }


                let total = 0;

                carrito.forEach((producto, index) => {
                    let div = document.createElement("div");
                    div.classList.add("flex", "items-center", "gap-4");

                    div.innerHTML = `
                                <div style="width: 6rem; height: 6rem; overflow: hidden; border-radius: 0.5rem;">
                                    ${producto.imagen ? `<img src="${producto.imagen}" alt="${producto.nombre}" style="width: 100%; height: 100%; object-fit: cover;">` : 
                                    `<div class="w-full h-full bg-gray-300 flex items-center justify-center text-gray-500"><span>Imagen No Disponible</span></div>`}
                                </div>
                                <div>
                                    <strong>${producto.nombre}</strong> - S/${producto.precio.toFixed(2)}
                                </div>
                                <button onclick="cambiarCantidad(${index}, -1)">➖</button>
                                <span>${producto.cantidad}</span>
                                <button onclick="cambiarCantidad(${index}, 1)">➕</button>
                                <button onclick="eliminarDelCarrito(${index})">❌</button>
                            `;

                    carritoLista.appendChild(div);
                    total += producto.precio * producto.cantidad;
                });

                totalPedido.innerText = total.toFixed(2);
                totalDetallePagar.innerText = (total + totalDetalle).toFixed(2);
                totalDetalleModal.innerText = totalDetalle.toFixed(2);

                actualizarContador();
                actualizarModal();
            }


            function cambiarCantidad(index, cambio) {
                let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

                if (carrito[index]) {
                    carrito[index].cantidad += cambio;
                    if (carrito[index].cantidad < 1) {
                        carrito[index].cantidad = 1; // Evita cantidades menores a 1
                    }
                }

                localStorage.setItem("carrito", JSON.stringify(carrito)); // Guardar en localStorage
                actualizarCarrito(); // Actualizar el carrito en pantalla
                actualizarModal(); // Asegurar que el modal también se actualice
                actualizarContador();
            }


            function eliminarDelCarrito(index) {
                let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

                if (index >= 0 && index < carrito.length) {
                    carrito.splice(index, 1); // Elimina el producto en la posición "index"
                    localStorage.setItem("carrito", JSON.stringify(carrito)); // 🔥 Guarda en localStorage
                    actualizarCarrito(); // 🔄 Refresca la vista del carrito
                    actualizarModal(); // 🔄 Refresca el modal también
                }
            }


            function actualizarPedido() {
                let carrito = JSON.parse(localStorage.getItem("carrito")) || []; // Recuperar el carrito desde localStorage

                if (carrito.length === 0) {
                    Swal.fire("Sin nuevos productos", "Debes agregar productos nuevos para actualizar el pedido.", "warning");
                    return;
                }

                axios.post("{{ route('pedidos.actualizar', ['id' => $pedido->id]) }}", {
                        productos: carrito
                    })
                    .then(response => {
                        Swal.fire("¡Éxito!", "Los productos fueron agregados correctamente al pedido.", "success")
                            .then(() => {
                                localStorage.removeItem(
                                    "carrito"); // 🔥 Eliminar carrito después de actualizar el pedido
                                location.reload();
                            });
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire("Error", "Hubo un problema al actualizar el pedido.", "error");
                    });
            }


            function pagarPedido() {
                Swal.fire({
                    title: "Confirmar Pago",
                    text: `¿Deseas pagar S/ ${totalDetalle.toFixed(2)} por el pedido actual?`,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sí, pagar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire("¡Pago exitoso!", "El pago se ha realizado correctamente.", "success");
                        // Aquí iría la lógica para enviar el pago con Stripe u otro método
                    }
                });
            }
            /*  */
            function actualizarContador() {
                let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

                let totalProductos = carrito.reduce((total, producto) => total + producto.cantidad, 0);

                let contadorProductos = document.getElementById("contadorProductos");

                if (contadorProductos) {
                    contadorProductos.innerText = totalProductos; // 🔄 Actualiza el contador visible en la UI
                }
            }
            document.getElementById("toggleAside").addEventListener("click", function() {
                let modal = document.getElementById("my-modal");
                modal.style.display = "block";
                modal.classList.add("modal-fade-in");
                actualizarModal(); // Actualiza el contenido del modal
            });

            function cerrarModal() {
                document.getElementById("my-modal").style.display = "none";
            }

            function actualizarModal() {
                let carritoListaModal = document.getElementById("carritoListaModal");
                let totalPedidoModal = document.getElementById("totalPedidoModal");
                let totalDetallePagarModal = document.getElementById("totalDetallePagarModal");
                let totalDetalleModal = document.getElementById("totalDetalleModal");

                let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

                // 🚨 Verifica que los elementos existen
                if (!carritoListaModal || !totalPedidoModal || !totalDetallePagarModal || !totalDetalleModal) {
                    console.error("Uno o más elementos del modal no existen en el DOM.");
                    return;
                }

                carritoListaModal.innerHTML = ""; // Limpiar el modal antes de actualizarlo
                let total = 0;

                carrito.forEach((producto, index) => {
                    let div = document.createElement("div");
                    div.classList.add("flex", "items-center", "gap-2");

                    div.innerHTML = `
                                <div style="width: 6rem; height: 6rem; overflow: hidden; border-radius: 0.5rem;">
                                    ${producto.imagen 
                                        ? `<img src="${producto.imagen}" alt="${producto.nombre}" style="width: 100%; height: 100%; object-fit: cover;">` 
                                        : `<div class="w-full h-full bg-gray-300 flex items-center justify-center text-gray-500"><span>Sin Imagen</span></div>`}
                                </div>
                                <div>
                                    <strong>${producto.nombre}</strong> S/${producto.precio.toFixed(2)}
                                </div>
                                <button onclick="cambiarCantidad(${index}, -1)">➖</button>
                                <span>${producto.cantidad}</span>
                                <button onclick="cambiarCantidad(${index}, 1)">➕</button>
                                <button onclick="eliminarDelCarrito(${index})">❌</button>
                            `;

                    carritoListaModal.appendChild(div);
                    total += producto.precio * producto.cantidad;
                });

                // 🔄 Actualizar totales en el modal
                totalPedidoModal.innerText = total.toFixed(2);
                totalDetallePagarModal.innerText = (total + totalDetalle).toFixed(2);
                totalDetalleModal.innerText = totalDetalle.toFixed(2);
            }


            function actualizarPedidoDesdeModal() {
                let carrito = JSON.parse(localStorage.getItem("carrito")) || []; // Recuperar el carrito desde localStorage

                if (carrito.length === 0) {
                    Swal.fire("Sin nuevos productos", "Debes agregar productos nuevos para actualizar el pedido.", "warning");
                    return;
                }

                axios.post("{{ route('pedidos.actualizar', ['id' => $pedido->id]) }}", {
                        productos: carrito
                    })
                    .then(response => {
                        Swal.fire("¡Éxito!", "Los productos fueron agregados correctamente al pedido.", "success")
                            .then(() => {
                                localStorage.removeItem(
                                    "carrito"); // 🔥 Limpiar el carrito después de actualizar el pedido
                                location.reload();
                            });
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire("Error", "Hubo un problema al actualizar el pedido.", "error");
                    });
            }
        </script>

        <div id="my-modal" class="modal">
            <div>
                <div>
                    <h2 style="text-align: center;">Lista para Acatualizar Pedido</h2>
                </div>

                <!-- Contenedor de Productos del Modal -->
                <div id="carritoModal" style="max-height: 300px; overflow-y: auto; margin-bottom: 10px;">
                    <div id="carritoListaModal"
                        style="display: flex; flex-wrap: wrap; gap: 1rem;height: 300px; overflow: auto;">
                        {{-- Productos agregados dinámicamente aquí --}}
                    </div>
                </div>

                <!-- Totales dentro del Modal -->
                <div class="flex justify-between m-2">
                    <p><strong>Subtotal: S/ <span class="text-blue-600" id="totalPedidoModal">0.00</span></strong></p>
                    <p><strong>Total: S/ <span class="text-blue-600" id="totalDetallePagarModal">0.00</span></strong>
                    </p>
                </div>

                <!-- Botón "Actualizar Pedido" -->
                <button type="button" onclick="actualizarPedidoDesdeModal()"
                    style="width: 100%; background: #0b7efa; color: white; padding: 10px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin-top: 10px;">
                    🔄 Actualizar Pedido
                </button>
                {{-- {{ route('procesarPagoMesa.stripe', ['pedido' => $pedido->id]) }}" --}}
                <!-- Botón "Pagar" -->
                <form action="{{ route('pedidoMesa.pagar', $pedido->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                        style="width: 100%; padding: 10px; background: #135287; color: white; border: none; 
                       border-radius: 5px; cursor: pointer; margin-top: 10px;">
                        Pagar S/ <span id="totalDetalleModal">0.00</span>
                    </button>

                </form>

                <!-- Botón "Cerrar Modal" -->
                <button type="button" onclick="cerrarModal()"
                    style="width: 100%; background: #d9534f; color: white; padding: 10px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin-top: 10px;">
                    ❌ Cerrar
                </button>
            </div>
        </div>

    </div>
    <script>
        document.getElementById('toggleCategorias').addEventListener('click', () => {
            const list = document.getElementById('categoriaList');
            list.classList.toggle('hidden');
        });
        document.addEventListener("DOMContentLoaded", () => {
            const buscador = document.getElementById("buscador");
            const productos = document.querySelectorAll(".producto");

            buscador.addEventListener("input", () => {
                let filtro = buscador.value.toLowerCase().trim();

                productos.forEach(producto => {
                    let nombreProducto = producto.getAttribute("data-nombre");

                    if (nombreProducto.includes(filtro)) {
                        producto.style.display = "flex"; // Muestra el producto
                    } else {
                        producto.style.display = "none"; // Oculta el producto
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const pedidoId = {{ $pedido->id }};

            // Función que pide el HTML actualizado y lo pone en la vista
            function actualizarDetallesCliente() {
                // ✅ Usa la ruta nombrada para evitar errores 404
                fetch(`{{ route('pedido.detalles.cliente', ['pedido_id' => $pedido->id]) }}`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('lista-productos-cliente').innerHTML = html;
                        console.log("✅ Detalles del cliente actualizados.");
                    })
                    .catch(error => console.error('❌ Error al actualizar detalles:', error));
            }

            function connectWebSocket() {
                const socket = new WebSocket("ws://127.0.0.1:8090");
                socket.onopen = () => console.log("🟢 Conectado como cliente.");
                socket.onclose = () => setTimeout(connectWebSocket, 3000);
                socket.onerror = (error) => console.error("🔴 Error:", error);

                socket.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        // Si el pedido fue actualizado, cancelado o completado...
                        if (data.action !== 'nuevo') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'info',
                                title: '¡Tu pedido fue actualizado!',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            // ...actualiza la lista de productos.
                            actualizarDetallesCliente();
                        }
                    } catch (e) {
                        console.error("Error al procesar mensaje:", event.data, e);
                    }
                };
            }
            connectWebSocket();
        });
    </script>
</body>

</html>
