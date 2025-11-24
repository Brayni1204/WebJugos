<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $empresa = \App\Models\Empresa::latest()->first();
        $favicon =
            $empresa && $empresa->favicon_url
                ? $empresa->favicon_url
                : asset('default-favicon.ico');
    @endphp
    <title>{{ $empresa->nombre }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
    <style>
        #opcionesProductoModal,
        #opcionesProductoOverlay {
            transition: opacity 0.3s ease-in-out;
            z-index: 9999;
        }

        #opcionesProductoModal {
            transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
        }

        #opcionesProductoModal.hidden {
            transform: translate(-50%, -50%) scale(0.95);
            opacity: 0;
            pointer-events: none;
        }

        #opcionesProductoOverlay.hidden {
            opacity: 0;
            pointer-events: none;
        }

        #modalCarrito #carritoListaModal {
            max-height: 350px;
            /* A bit larger, fixed height */
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Para alertas visuales -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div>
        <div class="flex flex-col lg:flex-row">
            <!-- 🛒 SECCIÓN DE PRODUCTOS -->
            <div class="flex-1 p-4">
                <div class="flex justify-center">
                    <h1 class="text-3xl font-bold text-gray-800 bg-gradient-to-r py-4 px-8">
                        Nuestra Carta
                    </h1>
                </div>
                <div>
                    <div>

                        <div class="flex flex-col items-center my-4 gap-4">
                            <div class="flex w-full justify-center gap-2">
                                <input type="text" id="buscador" placeholder="Buscar jugo..."
                                    class="w-1/2 p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                <button id="toggleCategorias"
                                    class="p-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 shadow-md transition">🧃
                                    Categorías</button>
                            </div>

                            <div id="categoriaList"
                                class="hidden flex flex-wrap justify-center gap-3 px-4 py-3 rounded-md bg-gray-100 border border-gray-300 shadow-inner w-full max-w-4xl transition-all duration-300">
                                @php
                                    $categoriaActiva = request()->get('categoria');
                                @endphp

                                <a href="{{ route('views.reservar', ['mesa' => request('mesa')]) }}"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition {{ !$categoriaActiva ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                                    Todos
                                </a>

                                @foreach ($categorias as $item)
                                    <a href="{{ route('views.reservar', ['mesa' => request('mesa'), 'categoria' => $item->id]) }}"
                                        class="px-4 py-2 rounded-full text-sm font-medium transition {{ $categoriaActiva == $item->id ? 'bg-blue-600 text-white shadow-md' : 'bg-blue-100 text-blue-800 hover:bg-blue-300' }}">
                                        {{ $item->nombre_categoria }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div id="listaProductos"
                            class="w-full grid gap-4 justify-center grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 px-4">
                            @foreach ($productos as $producto)
                                @php
                                    // 🛠️ CÓDIGO CORREGIDO para manejar la imagen de forma segura
                                    $primeraImagen = $producto->image->first();
                                    // Obtiene la URL si existe la imagen, de lo contrario, usa un string vacío.
                                    $urlRelativa = $primeraImagen ? $primeraImagen->url : '';

                                    // Construye la URL final, será 'storage/' si la urlRelativa es vacía, lo cual es inofensivo.
                                    $imagenUrl = $urlRelativa;

                                    // Si prefieres usar la URL de la imagen por defecto del proyecto si no hay imagen asociada:
                                    // $imagenUrl = $primeraImagen ? asset('storage/' . $primeraImagen->url) : asset('default-image.png');

                                @endphp
                                <button {{-- Aquí usamos la variable $imagenUrl precalculada, que nunca será 'null' --}}
                                    onclick="mostrarOpcionesProducto({{ $producto->id }}, '{{ $producto->nombre_producto }}', {{ $producto->precios->precio_venta ?? 0 }}, '{{ $imagenUrl }}')"
                                    class="producto bg-white border border-gray-300 rounded-lg p-4 shadow-md hover:shadow-xl hover:scale-105 transition transform duration-300 ease-in-out flex flex-col items-center"
                                    data-categoria="{{ $producto->categoria_id }}"
                                    data-nombre="{{ strtolower($producto->nombre_producto) }}">

                                    <div class="w-full aspect-[4/5] overflow-hidden rounded-md">
                                        @if ($producto->image->isNotEmpty())
                                            <img src="{{ $producto->image->first()->url }}"
                                                alt="{{ $producto->nombre_producto }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-gray-600">Imagen No Disponible</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-3 text-center">
                                        <strong
                                            class="block text-gray-800 text-lg">{{ $producto->nombre_producto }}</strong>
                                        <span
                                            class="text-xl font-semibold text-blue-700">S/{{ $producto->precios->precio_venta ?? 'N/A' }}</span>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- 🛒 CARRITO FIJO A LA DERECHA EN PANTALLAS GRANDES -->
                <div class="hidden lg:block w-80 min-w-[320px] bg-white shadow-lg rounded-lg p-2 sticky top-20 h-fit"
                    style="height: 96vh;">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4 text-center">Jugos Agregados</h2>
                        <div id="carrito" class="bg-gray-100 rounded-md shadow-sm p-2">
                            <div id="carritoLista" class="space-y-3" style="height: 500px; overflow: auto;">
                            </div>
                            <p class="text-lg font-semibold text-gray-700 mt-4">Total: <span id="totalPedido"
                                    class="text-blue-600">S/ 0.00</span></p>
                        </div>

                        <h3 class="text-xl font-semibold text-gray-800 mt-6">📩 Datos del Cliente (Opcional)</h3>
                        <form id="formularioCliente" class="mt-4">
                            <label for="nombre" class="block text-gray-700 font-medium">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ingrese su nombre"
                                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition">

                            <label for="correo" class="block text-gray-700 font-medium mt-3">Correo
                                Electrónico:</label>
                            <input type="email" id="correo" name="correo" placeholder="Ingrese su correo"
                                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition">

                            <button type="button" onclick="realizarPedido()"
                                class="w-full bg-blue-600 text-white font-semibold p-2 mt-4 rounded-md hover:bg-blue-700 transition">
                                Hacer Pedido
                            </button>
                        </form>
                    </div>
                </div>
            </div>


            <div id="modalCarrito"
                style="display: none; position: fixed;
                                    top: 50%; left: 50%;
                                    transform: translate(-50%, -50%);
                                    background: white;
                                    padding: 10px;
                                    border-radius: 10px;
                                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                                    width: 90%; max-width: 400px;
                                    z-index: 2000;
                                    max-height: 90vh; overflow-y: auto;">
                <div class="flex font-bold text-xs m-1">
                    <h2>Jugos Agregados a tu Pedido</h2>
                </div>
                <div id="carritoListaModal" style="overflow: auto;"></div>
                <div class="flex justify-end p-1">
                    <p><strong class="text-blue-600">Total:<span id="totalPedidoModal">0.00</span></strong></p>
                </div>

                <form id="formularioClienteModal" class="mb-4">
                    <label for="nombreModal" class="block text-gray-700 font-medium">Nombre:</label>
                    <input type="text" id="nombreModal" name="nombre" placeholder="Ingrese su nombre (Opcional)"
                        autocomplete="off"
                        class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition mb-1">

                    <label for="correoModal" class="block text-gray-700 font-medium">Correo Electrónico:</label>
                    <input type="email" id="correoModal" name="correo" placeholder="Ingrese su correo (Opcional)"
                        autocomplete="off"
                        class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </form>

                <div class="flex items-center gap-3 mt-4">
                    <button type="button" onclick="realizarPedidoDesdeModal()"
                        class="flex-1 text-center py-2 px-4 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition-colors shadow-md shadow-blue-500/30">
                        Hacer Pedido
                    </button>
                    <button type="button" onclick="cerrarModalCarrito()"
                        class="flex-1 text-center py-2 px-4 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>

            <button id="toggleAsideAgregarModal"
                class="lg:hidden fixed bottom-5 right-5 bg-blue-600 text-white w-16 h-16 rounded-full flex items-center justify-center shadow-lg text-2xl hover:bg-blue-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span id="contadorProductoAgregarModal"
                    class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center">0</span>
            </button>

            <!-- Fondo Oscuro cuando el modal está activo -->
            <div id="modalOverlay"
                style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1500;"
                onclick="cerrarModalCarrito()">
            </div>

            <!-- Modal para Opciones de Producto -->
            <div id="opcionesProductoOverlay" onclick="cerrarOpcionesProductoModal()"
                class="hidden fixed inset-0 bg-black bg-opacity-60 z-40"></div>

            <div id="opcionesProductoModal"
                class="hidden fixed top-1/2 left-1/2 w-11/12 max-w-sm bg-white rounded-2xl shadow-xl z-50 p-6 transform -translate-x-1/2 -translate-y-1/2 max-h-[90vh] overflow-y-auto">
                <h3 id="opcionesProductoNombre" class="text-2xl font-bold text-gray-800 mb-2 text-center"></h3>
                <p class="text-center text-gray-500 mb-6">Puedes Agregar Sugerencias</p>

                <div class="space-y-5">
                    <!-- Temperatura -->
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Temperatura</h4>
                        <div class="flex flex-wrap gap-2 grupo-temperatura">
                            <label class="flex-1">
                                <input type="checkbox" name="caracteristicas_modal[]" value="helado" class="hidden">
                                <span
                                    class="block text-center p-2 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer transition-all duration-200">Helado</span>
                            </label>
                            <label class="flex-1">
                                <input type="checkbox" name="caracteristicas_modal[]" value="temperado"
                                    class="hidden">
                                <span
                                    class="block text-center p-2 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer transition-all duration-200">Temperado</span>
                            </label>
                            <label class="flex-1">
                                <input type="checkbox" name="caracteristicas_modal[]" value="temperatura ambiente"
                                    class="hidden">
                                <span
                                    class="block text-center p-2 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer transition-all duration-200">Ambiente</span>
                            </label>
                        </div>
                    </div>

                    <!-- Azúcar -->
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Nivel de Azúcar</h4>
                        <div class="flex flex-wrap gap-2 grupo-azucar">
                            <label class="flex-1">
                                <input type="checkbox" name="caracteristicas_modal[]" value="con azúcar"
                                    class="hidden">
                                <span
                                    class="block text-center p-2 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer transition-all duration-200">Normal</span>
                            </label>
                            <label class="flex-1">
                                <input type="checkbox" name="caracteristicas_modal[]" value="bajo en azúcar"
                                    class="hidden">
                                <span
                                    class="block text-center p-2 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer transition-all duration-200">Bajo</span>
                            </label>
                            <label class="flex-1">
                                <input type="checkbox" name="caracteristicas_modal[]" value="sin azúcar"
                                    class="hidden">
                                <span
                                    class="block text-center p-2 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer transition-all duration-200">Sin
                                    Azúcar</span>
                            </label>
                        </div>
                    </div>

                    <!-- Observación -->
                    <div>
                        <textarea id="observacion" name="observacion" rows="2" placeholder="Observación adicional"
                            class="w-full p-2 mt-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"></textarea>
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6 pt-4 border-t">
                    <button type="button" onclick="cerrarOpcionesProductoModal()"
                        class="flex-1 text-center py-2 px-4 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition-colors">Cancelar</button>
                    <button type="button" id="anadirAlCarritoBtn"
                        class="flex-1 text-center py-2 px-4 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition-colors shadow-md shadow-blue-500/30">Añadir</button>
                </div>
            </div>

            <script>
                let productoParaAnadir = null;

                function mostrarOpcionesProducto(id, nombre, precio, imagenUrl) {
                    productoParaAnadir = {
                        id,
                        nombre,
                        precio,
                        imagenUrl
                    };
                    document.getElementById('opcionesProductoNombre').innerText = nombre;

                    // Reset checkboxes and observation
                    document.querySelectorAll('#opcionesProductoModal input[type="checkbox"]').forEach(cb => {
                        cb.checked = false;
                        const span = cb.nextElementSibling;
                        span.classList.remove('bg-blue-600', 'text-white', 'border-blue-500', 'shadow-md');
                        span.classList.add('bg-gray-50', 'border-gray-200');
                    });
                    document.getElementById('observacion').value = '';

                    document.getElementById('opcionesProductoModal').classList.remove('hidden');
                    document.getElementById('opcionesProductoOverlay').classList.remove('hidden');

                    document.getElementById('anadirAlCarritoBtn').onclick = () => agregarAlCarritoDesdeModal();
                }

                function cerrarOpcionesProductoModal() {
                    const modal = document.getElementById('opcionesProductoModal');
                    modal.classList.add('hidden');
                    document.getElementById('opcionesProductoOverlay').classList.add('hidden');
                    productoParaAnadir = null;
                }

                function agregarAlCarritoDesdeModal() {
                    if (!productoParaAnadir) return;

                    let caracteristicasSeleccionadas = Array.from(document.querySelectorAll(
                            '#opcionesProductoModal input[name="caracteristicas_modal[]"]:checked'))
                        .map(cb => cb.value);

                    let observacion = document.getElementById('observacion').value.trim();
                    if (observacion) {
                        caracteristicasSeleccionadas.push(observacion);
                    }

                    agregarAlCarrito(productoParaAnadir.id, productoParaAnadir.nombre, productoParaAnadir.precio,
                        productoParaAnadir.imagenUrl, caracteristicasSeleccionadas);

                    cerrarOpcionesProductoModal();
                }

                document.addEventListener("DOMContentLoaded", () => {
                    actualizarCarritoAgregar();
                    actualizarContadorAgregar();

                    document.querySelectorAll('.grupo-temperatura label, .grupo-azucar label').forEach(label => {
                        const checkbox = label.querySelector('input[type="checkbox"]');
                        const span = label.querySelector('span');

                        const updateGroupStyle = (groupClass) => {
                            document.querySelectorAll(`.${groupClass} label`).forEach(l => {
                                const cb = l.querySelector('input[type="checkbox"]');
                                const sp = l.querySelector('span');
                                if (cb.checked) {
                                    sp.classList.add('bg-blue-600', 'text-white', 'border-blue-500',
                                        'shadow-md');
                                    sp.classList.remove('bg-gray-50', 'border-gray-200');
                                } else {
                                    sp.classList.remove('bg-blue-600', 'text-white',
                                        'border-blue-500', 'shadow-md');
                                    sp.classList.add('bg-gray-50', 'border-gray-200');
                                }
                            });
                        };

                        checkbox.addEventListener('change', () => {
                            const group = label.parentElement.classList.contains('grupo-temperatura') ?
                                'grupo-temperatura' : 'grupo-azucar';
                            if (checkbox.checked) {
                                document.querySelectorAll(`.${group} input[type="checkbox"]`).forEach(
                                    otherCheckbox => {
                                        if (otherCheckbox !== checkbox) {
                                            otherCheckbox.checked = false;
                                        }
                                    });
                            }
                            updateGroupStyle(group);
                        });
                    });
                });

                function actualizarContadorAgregar() {
                    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
                    let totalProductos = carrito.reduce((total, producto) => total + producto.cantidad, 0);
                    let contadorModal = document.getElementById("contadorProductoAgregarModal");
                    if (contadorModal) contadorModal.innerText = totalProductos;
                }

                document.addEventListener("DOMContentLoaded", () => {
                    let botonToggle = document.getElementById("toggleAsideAgregarModal");
                    if (!botonToggle) return;
                    botonToggle.addEventListener("click", () => mostrarModalCarrito());
                });

                function mostrarModalCarrito() {
                    let modal = document.getElementById("modalCarrito");
                    let overlay = document.getElementById("modalOverlay");
                    if (!modal || !overlay) return;
                    modal.style.display = "block";
                    overlay.style.display = "block";
                    actualizarModalCarrito();
                }

                function cerrarModalCarrito() {
                    document.getElementById("modalCarrito").style.display = "none";
                    document.getElementById("modalOverlay").style.display = "none";
                }

                function actualizarModalCarrito() {
                    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
                    let carritoListaModal = document.getElementById("carritoListaModal");
                    let totalPedidoModal = document.getElementById("totalPedidoModal");
                    let contadorProductoAgregarModal = document.getElementById("contadorProductoAgregarModal");

                    if (!carritoListaModal || !totalPedidoModal || !contadorProductoAgregarModal) return;

                    carritoListaModal.innerHTML = "";
                    let total = 0;
                    let totalProductos = 0;

                    carrito.forEach((producto, index) => {
                        let caracteristicasHtml = '';
                        if (producto.caracteristicas && producto.caracteristicas.length > 0) {
                            const tags = producto.caracteristicas.map(c =>
                                `<span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">${c}</span>`
                            ).join(' ');
                            caracteristicasHtml = `<div class="mt-1.5 flex flex-wrap gap-1.5">${tags}</div>`;
                        }

                        let div = document.createElement("div");
                        div.innerHTML = `
                        <div class="flex items-center bg-white p-1 rounded-lg shadow-sm w-full gap-3">
                            <div class="flex h-full items-center">
                                <div class="w-16 h-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                                    <img src="${producto.imagenUrl || 'https://via.placeholder.com/150'}" alt="${producto.nombre}" class="w-full h-full object-cover">
                                </div>
                            </div>
                            <div class="flex-grow">
                                <div class="flex items-start bg-white p-1 rounded-lg shadow-sm w-full gap-3 justify-between">
                                    <div>
                                        <p class="font-bold text-gray-800 text-xs">${producto.nombre}</p>
                                        <p class="text-sm text-gray-500">S/${producto.precio.toFixed(2)}</p>
                                    </div>
                                    <div class="flex flex-col justify-between h-full">
                                        <div class="flex items-center gap-2">
                                            <button onclick="cambiarCantidad(${index}, -1)" class="w-6 h-6 bg-gray-200 text-gray-600 rounded-full font-bold flex items-center justify-center transition hover:bg-gray-300">-</button>
                                            <span class="font-bold text-lg">${producto.cantidad}</span>
                                            <button onclick="cambiarCantidad(${index}, 1)" class="w-6 h-6 bg-gray-200 text-gray-600 rounded-full font-bold flex items-center justify-center transition hover:bg-gray-300">+</button>
                                        </div>
                                        <button onclick="eliminarDelCarrito(${index})" class="text-xs text-red-500 hover:text-red-700 transition mt-2">Quitar</button>
                                    </div>
                                </div>
                                ${caracteristicasHtml}
                            </div>
                        </div>`;
                        carritoListaModal.appendChild(div);
                        total += producto.precio * producto.cantidad;
                        totalProductos += producto.cantidad;
                    });

                    totalPedidoModal.innerText = `S/ ${total.toFixed(2)}`;
                    contadorProductoAgregarModal.innerText = totalProductos;

                    let nombreInput = document.getElementById("nombre");
                    let correoInput = document.getElementById("correo");
                    let nombreModalInput = document.getElementById("nombreModal");
                    let correoModalInput = document.getElementById("correoModal");

                    if (nombreInput && correoInput && nombreModalInput && correoModalInput) {
                        nombreModalInput.value = nombreInput.value;
                        correoModalInput.value = correoInput.value;
                    }
                }

                function realizarPedidoDesdeModal() {
                    let mesaUuid = new URLSearchParams(window.location.search).get('mesa');
                    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

                    if (carrito.length === 0) {
                        Swal.fire("Carrito vacío", "Agrega productos antes de realizar el pedido.", "warning");
                        return;
                    }

                    let nombre = document.getElementById("nombreModal").value;
                    let correo = document.getElementById("correoModal").value;

                    axios.post("{{ route('pedidos.store') }}", {
                            mesa_id: mesaUuid,
                            productos: carrito,
                            nombre: nombre || null,
                            correo: correo || null
                        })
                        .then(response => {
                            cerrarModalCarrito();
                            setTimeout(() => {
                                Swal.fire("¡Éxito!", "Pedido realizado con éxito.", "success").then(() => {
                                    localStorage.removeItem("carrito");
                                    actualizarContadorAgregar();
                                    window.location.href = response.data.redirect;
                                });
                            }, 300);
                        })
                        .catch(error => {
                            console.error(error);
                            Swal.fire("Error", "Error al realizar el pedido: " + (error.response?.data?.error ||
                                "Error desconocido"), "error");
                        });
                }

                document.addEventListener("DOMContentLoaded", () => {
                    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
                    if (!Array.isArray(carrito)) {
                        carrito = [];
                        localStorage.setItem("carrito", JSON.stringify(carrito));
                    }
                    actualizarCarritoAgregar();
                });

                function agregarAlCarrito(id, nombre, precio, imagenUrl, caracteristicas) {
                    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
                    const caracteristicasKey = (caracteristicas || []).sort().join(',');
                    let productoEnCarrito = carrito.find(p => p.id === id && ((p.caracteristicas || []).sort().join(
                        ',') === caracteristicasKey));

                    if (productoEnCarrito) {
                        productoEnCarrito.cantidad += 1;
                    } else {
                        carrito.push({
                            id,
                            nombre,
                            precio,
                            cantidad: 1,
                            imagenUrl,
                            caracteristicas: caracteristicas || []
                        });
                    }

                    localStorage.setItem("carrito", JSON.stringify(carrito));
                    actualizarCarritoAgregar();
                    actualizarContadorAgregar();
                    actualizarModalCarrito();
                }

                function actualizarCarritoAgregar() {
                    let carritoLista = document.getElementById("carritoLista");
                    let totalPedido = document.getElementById("totalPedido");

                    if (!carritoLista || !totalPedido) return;

                    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
                    carritoLista.innerHTML = "";
                    let total = 0;

                    if (carrito.length === 0) {
                        carritoLista.innerHTML = '<p class="text-center text-gray-500 p-4">Tu carrito está vacío.</p>';
                    } else {
                        carrito.forEach((producto, index) => {
                            let caracteristicasHtml = '';
                            if (producto.caracteristicas && producto.caracteristicas.length > 0) {
                                const tags = producto.caracteristicas.map(c =>
                                    `<span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">${c}</span>`
                                ).join(' ');
                                caracteristicasHtml = `<div class="mt-1.5 flex flex-wrap gap-1.5">${tags}</div>`;
                            }

                            let div = document.createElement("div");
                            div.className = "bg-white p-3 rounded-lg shadow-sm w-full";
                            div.innerHTML = `
                                <div class="flex items-center bg-white p-1 rounded-lg shadow-sm w-full gap-3">
                                    <div class="flex h-full items-center">
                                        <div class="w-16 h-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                                            <img src="${producto.imagenUrl || 'https://via.placeholder.com/150'}" alt="${producto.nombre}" class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                    <div class="flex-grow">
                                        <div class="flex items-start bg-white p-1 rounded-lg shadow-sm w-full gap-3 justify-between">
                                            <div>
                                                <p class="font-bold text-gray-800 text-xs">${producto.nombre}</p>
                                                <p class="text-sm text-gray-500">S/${producto.precio.toFixed(2)}</p>
                                            </div>
                                            <div class="flex flex-col justify-between h-full">
                                                <div class="flex items-center gap-2">
                                                    <button onclick="cambiarCantidad(${index}, -1)" class="w-6 h-6 bg-gray-200 text-gray-600 rounded-full font-bold flex items-center justify-center transition hover:bg-gray-300">-</button>
                                                    <span class="font-bold text-lg">${producto.cantidad}</span>
                                                    <button onclick="cambiarCantidad(${index}, 1)" class="w-6 h-6 bg-gray-200 text-gray-600 rounded-full font-bold flex items-center justify-center transition hover:bg-gray-300">+</button>
                                                </div>
                                                <button onclick="eliminarDelCarrito(${index})" class="text-xs text-red-500 hover:text-red-700 transition mt-2">Quitar</button>
                                            </div>
                                        </div>
                                        ${caracteristicasHtml}
                                    </div>
                                </div>
                            `;
                            carritoLista.appendChild(div);
                            total += producto.precio * producto.cantidad;
                        });
                    }

                    totalPedido.innerText = `S/ ${total.toFixed(2)}`;
                    actualizarContadorAgregar();
                }

                function cambiarCantidad(index, cambio) {
                    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
                    if (carrito[index]) {
                        carrito[index].cantidad += cambio;
                        if (carrito[index].cantidad < 1) {
                            // If quantity is zero or less, remove the item
                            carrito.splice(index, 1);
                        }
                        localStorage.setItem("carrito", JSON.stringify(carrito));
                        actualizarCarritoAgregar();
                        actualizarContadorAgregar();
                        actualizarModalCarrito();
                    }
                }

                function eliminarDelCarrito(index) {
                    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
                    carrito.splice(index, 1);
                    localStorage.setItem("carrito", JSON.stringify(carrito));
                    actualizarCarritoAgregar();
                    actualizarContadorAgregar();
                    actualizarModalCarrito();
                }

                function realizarPedido() {
                    let mesaUuid = new URLSearchParams(window.location.search).get('mesa');
                    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

                    if (carrito.length === 0) {
                        Swal.fire("Carrito vacío", "Agrega productos antes de realizar el pedido.", "warning");
                        return;
                    }

                    let nombre = document.getElementById("nombre").value;
                    let correo = document.getElementById("correo").value;

                    axios.post("{{ route('pedidos.store') }}", {
                            mesa_id: mesaUuid,
                            productos: carrito,
                            nombre: nombre || null,
                            correo: correo || null
                        })
                        .then(response => {
                            Swal.fire("¡Éxito!", "Pedido realizado con éxito.", "success").then(() => {
                                localStorage.removeItem("carrito");
                                actualizarContadorAgregar();
                                window.location.href = response.data.redirect;
                            });
                        })
                        .catch(error => {
                            console.error(error);
                            Swal.fire("Error", "Error al realizar el pedido: " + (error.response?.data?.error ||
                                "Error desconocido"), "error");
                        });
                }

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

                document.addEventListener("DOMContentLoaded", () => {
                    const btnToggle = document.getElementById("toggleCategorias");
                    const contenedorCategorias = document.getElementById("categoriaList");

                    btnToggle.addEventListener("click", () => {
                        contenedorCategorias.classList.toggle("hidden");
                    });

                    const botones = document.querySelectorAll(".filtro-categoria");
                    const productos = document.querySelectorAll(".producto");

                    botones.forEach(btn => {
                        btn.addEventListener("click", () => {
                            const categoria = btn.getAttribute("data-categoria");

                            productos.forEach(producto => {
                                const categoriaProducto = producto.getAttribute("data-categoria");

                                if (categoria === "all" || categoria === categoriaProducto) {
                                    producto.style.display = "flex";
                                } else {
                                    producto.style.display = "none";
                                }
                            });
                        });
                    });
                });
            </script>
        </div>
</body>

</html>
