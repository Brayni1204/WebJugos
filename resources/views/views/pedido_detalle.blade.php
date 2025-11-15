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
    </style>
</head>

<body>
    <div style="width: 100%">
        <div>
            <div class="contenedor" style="display: flex; width: 100%;">
                <div class="contenedor--pedido">
                    <div style="padding: 10px">
                        <div style="padding: 10px">
                            <div style="display: flex; justify-content: center">
                                <h1>Pedidos Mesa N° - {{ $mesa->id }}</h1>
                            </div>
                        </div>
                        <div>
                            <div class="text-center mb-6">
                                <h2 class="text-2xl font-bold text-gray-800">Tu Pedido Actual</h2>
                            </div>

                            <div id="lista-productos-cliente" class="space-y-3 px-4 overflow-y-auto"
                                style="max-height: 12.5rem;">

                                @foreach ($pedido->detalles as $detalle)
                                    @php
                                        $descripcion = $detalle->descripcion;
                                        $nombreBase = $detalle->nombre_producto;
                                        $customizaciones = '';
                                        $customTags = []; // Inicializa el array de tags
                                        if (str_contains($descripcion, '(')) {
                                            $parts = explode('(', $descripcion, 2);
                                            $nombreBase = trim($parts[0]);
                                            $customizaciones = rtrim(trim($parts[1]), ')');
                                            if (!empty($customizaciones)) {
                                                // Divide las customizaciones en tags individuales
                                                $customTags = explode(',', $customizaciones);
                                            }
                                        }
                                    @endphp

                                    <div class="bg-white p-3 rounded-lg shadow-sm w-full">
                                        <div class="flex items-center gap-3">

                                            <div
                                                class="w-16 h-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                                                @if ($detalle->producto->image->isNotEmpty())
                                                    <img src="{{ asset('storage/' . $detalle->producto->image->first()->url) }}"
                                                        alt="{{ $nombreBase }}" class="w-full h-full object-cover">
                                                @else
                                                    <div
                                                        class="w-full h-full bg-gray-100 flex items-center justify-center">
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
                                            </div>

                                            <div class="flex-grow">
                                                <div class="flex items-start justify-between">
                                                    <div>
                                                        <p class="font-bold text-gray-800 text-xs">{{ $nombreBase }}
                                                        </p>
                                                        <p class="text-sm text-gray-500">
                                                            S/{{ number_format($detalle->precio_total, 2) }}
                                                        </p>
                                                    </div>
                                                    <div
                                                        class="bg-blue-600 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center shadow-md flex-shrink-0">
                                                        {{ $detalle->cantidad }}x
                                                    </div>
                                                </div>

                                                @if (!empty($customTags))
                                                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                                                        @foreach ($customTags as $tag)
                                                            <span
                                                                class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">{{ trim($tag) }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>

                    <div style="padding: 0px">
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
                                        onclick="mostrarOpcionesProducto({{ $producto->id }}, '{{ $producto->nombre_producto }}', {{ $producto->precios->precio_venta ?? 0 }}, '{{ $imageUrl }}')"
                                        class="producto bg-white border border-gray-300 rounded-lg p-4 shadow-md hover:shadow-xl hover:scale-105 transition transform duration-300 ease-in-out flex flex-col items-center"
                                        data-nombre="{{ strtolower($producto->nombre_producto) }}">
                                        <div class="w-full aspect-[4/5] overflow-hidden rounded-md">
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
            class="sm:hidden fixed bottom-5 right-5 bg-blue-600 text-white w-16 h-16 rounded-full flex items-center justify-center shadow-lg text-2xl hover:bg-blue-700 transition-colors z-1000">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg><span id="contadorProductos"
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center">0</span>
        </button>

        <!-- Modal para Opciones de Producto -->
        <div id="opcionesProductoOverlay" onclick="cerrarOpcionesProductoModal()"
            class="hidden fixed inset-0 bg-black bg-opacity-60 z-40"></div>

        <div id="opcionesProductoModal"
            class="hidden fixed top-1/2 left-1/2 w-11/12 max-w-sm bg-white rounded-2xl shadow-xl z-50 p-6 transform -translate-x-1/2 -translate-y-1/2">
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
                                class="block text-center p-1 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer transition-all duration-200">Helado</span>
                        </label>
                        <label class="flex-1">
                            <input type="checkbox" name="caracteristicas_modal[]" value="temperado" class="hidden">
                            <span
                                class="block text-center p-1 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer transition-all duration-200">Temperado</span>
                        </label>
                        <label class="flex-1">
                            <input type="checkbox" name="caracteristicas_modal[]" value="temperatura ambiente"
                                class="hidden">
                            <span
                                class="block text-center p-1 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer transition-all duration-200">Ambiente</span>
                        </label>
                    </div>
                </div>

                <!-- Azúcar -->
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Nivel de Azúcar</h4>
                    <div class="flex flex-wrap gap-2 grupo-azucar">
                        <label class="flex-1">
                            <input type="checkbox" name="caracteristicas_modal[]" value="con azúcar" class="hidden">
                            <span
                                class="block text-center p-1 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer transition-all duration-200">Normal</span>
                        </label>
                        <label class="flex-1">
                            <input type="checkbox" name="caracteristicas_modal[]" value="bajo en azúcar"
                                class="hidden">
                            <span
                                class="block text-center p-1 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer transition-all duration-200">Bajo</span>
                        </label>
                        <label class="flex-1">
                            <input type="checkbox" name="caracteristicas_modal[]" value="sin azúcar" class="hidden">
                            <span
                                class="block text-center p-1 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer transition-all duration-200">Sin
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

            <div class="flex items-center gap-4 mt-8">
                <button type="button" onclick="cerrarOpcionesProductoModal()"
                    class="w-full text-center py-3 px-4 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition">Cancelar</button>
                <button type="button" id="anadirAlCarritoBtn"
                    class="w-full text-center py-3 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-lg shadow-blue-500/20">Añadir
                </button>
            </div>
        </div>
        <div id="opcionesProductoOverlay"
            style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 2500;"
            onclick="cerrarOpcionesProductoModal()"></div>

        <script>
            let productoParaAnadir = null;

            function mostrarOpcionesProducto(id, nombre, precio, imagen) {
                productoParaAnadir = {
                    id,
                    nombre,
                    precio,
                    imagen
                };
                document.getElementById('opcionesProductoNombre').innerText = nombre;

                // Reset
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
                document.getElementById('opcionesProductoModal').classList.add('hidden');
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
                    productoParaAnadir.imagen, caracteristicasSeleccionadas);

                cerrarOpcionesProductoModal();
            }

            document.addEventListener("DOMContentLoaded", () => {
                actualizarCarrito();
                actualizarContador();

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

            let carrito = [];
            let totalDetalle = {{ $pedido->detalles->sum('precio_total') }}; // Suma de los productos ya agregados en el pedido
            document.getElementById("totalDetalle").innerText = totalDetalle.toFixed(2);

            function agregarAlCarrito(id, nombre, precio, imagen, caracteristicas) {
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
                        imagen: imagen || null,
                        caracteristicas: caracteristicas || []
                    });
                }

                localStorage.setItem("carrito", JSON.stringify(carrito)); // Guardar cambios en localStorage
                actualizarCarrito();
                actualizarContador();
                actualizarModal();
            }


            function actualizarCarrito() {
                const carritoLista = document.getElementById("carritoLista");
                const totalPedido = document.getElementById("totalPedido");
                const totalDetallePagar = document.getElementById("totalDetallePagar");

                if (!carritoLista || !totalPedido || !totalDetallePagar) {
                    return; // Sidebar not present, do nothing
                }

                const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
                let total = 0;
                carritoLista.innerHTML = ""; // Clear the list

                if (carrito.length === 0) {
                    carritoLista.innerHTML = '<p class="text-center text-gray-500 p-4">Añade productos para actualizar.</p>';
                } else {
                    carrito.forEach((producto, index) => {
                        let caracteristicasHtml = '';
                        if (producto.caracteristicas && producto.caracteristicas.length > 0) {
                            const tags = producto.caracteristicas.map(c =>
                                `<span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">${c}</span>`
                            ).join(' ');
                            caracteristicasHtml = `<div class="mt-1.5 flex flex-wrap gap-1.5">${tags}</div>`;
                        }

                        const div = document.createElement("div");
                        div.className = "bg-white p-3 rounded-lg shadow-sm w-full";
                        div.innerHTML = `
                        <div class="flex items-center bg-white p-1 rounded-lg shadow-sm w-full gap-3">
                            <div class="flex h-full items-center">
                                <div class="w-16 h-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                                    <img src="${producto.imagen || 'https://via.placeholder.com/150'}" alt="${producto.nombre}" class="w-full h-full object-cover">
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
                        carritoLista.appendChild(div);
                        total += producto.precio * producto.cantidad;
                    });
                }

                totalPedido.innerText = total.toFixed(2);
                totalDetallePagar.innerText = (total + totalDetalle).toFixed(2);
            }


            function cambiarCantidad(index, cambio) {
                let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

                if (carrito[index]) {
                    carrito[index].cantidad += cambio;
                    if (carrito[index].cantidad < 1) {
                        carrito.splice(index, 1);
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
                    Swal.fire("Sin nuevos productos", "Debes agregar productos nuevos para actualizar el pedido.",
                        "warning");
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
                const carritoListaModal = document.getElementById("carritoListaModal");
                const totalPedidoModal = document.getElementById("totalPedidoModal");
                const totalDetallePagarModal = document.getElementById("totalDetallePagarModal");
                const totalPagarBotonModal = document.querySelector('#my-modal #totalDetalleModal');

                if (!carritoListaModal || !totalPedidoModal || !totalDetallePagarModal || !totalPagarBotonModal) {
                    return; // Modal not present or open, do nothing
                }

                const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
                let total = 0;
                carritoListaModal.innerHTML = ""; // Clear the list

                if (carrito.length === 0) {
                    carritoListaModal.innerHTML =
                        '<p class="text-center text-gray-500 p-4">Añade productos para actualizar.</p>';
                } else {
                    carrito.forEach((producto, index) => {
                        let caracteristicasHtml = '';
                        if (producto.caracteristicas && producto.caracteristicas.length > 0) {
                            const tags = producto.caracteristicas.map(c =>
                                `<span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">${c}</span>`
                            ).join(' ');
                            caracteristicasHtml = `<div class="mt-1.5 flex flex-wrap gap-1.5">${tags}</div>`;
                        }

                        const div = document.createElement("div");
                        div.className = "bg-white p-3 rounded-lg shadow-sm w-full";
                        div.innerHTML = `
                        <div class="flex items-center bg-white p-1 rounded-lg shadow-sm w-full gap-3">
                            <div class="flex h-full items-center">
                                <div class="w-16 h-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200">
                                    <img src="${producto.imagen || 'https://via.placeholder.com/150'}" alt="${producto.nombre}" class="w-full h-full object-cover">
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
                    });
                }

                // Update totals in the modal
                totalPedidoModal.innerText = total.toFixed(2);
                const grandTotal = total + totalDetalle;
                totalDetallePagarModal.innerText = grandTotal.toFixed(2);
                totalPagarBotonModal.innerText = grandTotal.toFixed(2);
            }


            function actualizarPedidoDesdeModal() {
                let carrito = JSON.parse(localStorage.getItem("carrito")) || []; // Recuperar el carrito desde localStorage

                if (carrito.length === 0) {
                    Swal.fire("Sin nuevos productos", "Debes agregar productos nuevos para actualizar el pedido.",
                        "warning");
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
                    <p><strong class="text-blue-600">Subtotal: S/ <span class="text-blue-600"
                                id="totalPedidoModal">0.00</span></strong></p>
                    <p><strong class="text-blue-600">Total: S/ <span class="text-blue-600"
                                id="totalDetallePagarModal">0.00</span></strong>
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Botón "Actualizar Pedido" -->
                    <button type="button" onclick="actualizarPedidoDesdeModal()"
                        class="flex-1 text-center py-2 px-2 text-sm bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition-colors">Actualizar
                    </button>
                    {{-- {{ route('procesarPagoMesa.stripe', ['pedido' => $pedido->id]) }}" --}}
                    <!-- Botón "Pagar" -->
                    <form action="{{ route('pedidoMesa.pagar', $pedido->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full text-center py-2 px-2 text-sm bg-green-600 text-white font-semibold rounded-md hover:bg-green-700 transition-colors">
                            Pagar S/ <span id="totalDetalleModal">0.00</span>
                        </button>

                    </form>
                    <!-- Botón "Cerrar Modal" -->
                    <button type="button" onclick="cerrarModal()"
                        class="flex-1 text-center py-2 px-2 text-sm bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition-colors">Cerrar
                    </button>
                </div>
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

            function actualizarDetallesCliente() {
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
