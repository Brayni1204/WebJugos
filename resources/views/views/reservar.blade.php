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
    <title>{{ $empresa->nombre }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
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
                                    $imagenUrl = asset('storage/' . $urlRelativa);

                                    // Si prefieres usar la URL de la imagen por defecto del proyecto si no hay imagen asociada:
                                    // $imagenUrl = $primeraImagen ? asset('storage/' . $primeraImagen->url) : asset('default-image.png');

                                @endphp
                                <button {{-- Aquí usamos la variable $imagenUrl precalculada, que nunca será 'null' --}}
                                    onclick="agregarAlCarrito({{ $producto->id }}, '{{ $producto->nombre_producto }}', {{ $producto->precios->precio_venta ?? 0 }}, '{{ $imagenUrl }}')"
                                    class="producto bg-white border border-gray-300 rounded-lg p-4 shadow-md hover:shadow-xl hover:scale-105 transition transform duration-300 ease-in-out flex flex-col items-center"
                                    data-categoria="{{ $producto->categoria_id }}"
                                    data-nombre="{{ strtolower($producto->nombre_producto) }}">

                                    <div class="w-full aspect-[4/5] overflow-hidden rounded-md">
                                        @if ($producto->image->isNotEmpty())
                                            <img src="{{ asset('storage/' . $producto->image->first()->url) }}"
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
                        <div id="carrito" class="bg-gray-100 rounded-md shadow-sm">
                            <div id="carritoLista" class="space-y-2" style="height: 500px; overflow: auto;"></div>
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
                        z-index: 2000;">
                <div class="flex justify-center m-2">
                    <h2>Jugos Agregados a tu Pedido</h2>
                </div>
                <div id="carritoListaModal" style="height: 250px; overflow: auto;"></div>
                <div class="flex justify-center m-2">
                    <p><strong class="text-blue-600">Total: S/ <span id="totalPedidoModal">0.00</span></strong></p>
                </div>

                <h3>📩 Datos del Cliente (Opcional)</h3>
                <form id="formularioClienteModal">
                    <label for="nombreModal">Nombre:</label>
                    <input type="text" id="nombreModal" name="nombre" placeholder="Ingrese su nombre"
                        autocomplete="off"
                        style="width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ccc; border-radius: 5px;">

                    <label for="correoModal">Correo Electrónico:</label>
                    <input type="email" id="correoModal" name="correo" placeholder="Ingrese su correo"
                        autocomplete="off"
                        style="width: 100%; padding: 8px; margin: 5px 0; border: 1px solid #ccc; border-radius: 5px;">

                    <button type="button" onclick="realizarPedidoDesdeModal()"
                        style="width: 100%; padding: 10px; background: #135287; color: white; border: none; 
                       border-radius: 5px; cursor: pointer; margin-top: 10px;">
                        Hacer Pedido
                    </button>
                </form>

                <button onclick="cerrarModalCarrito()"
                    style="width: 100%; padding: 10px; background: red; color: white; border: none; 
               border-radius: 5px; cursor: pointer; margin-top: 10px;">
                    Cerrar
                </button>
            </div>

            <button id="toggleAsideAgregarModal"
                style="background: #135287; color: white; border: none; padding: 12px 16px; border-radius: 50%; font-size: 16px; cursor: pointer; margin-top: 10px; position: fixed; bottom: 20px; right: 20px; ">
                🛒 <span id="contadorProductoAgregarModal">0</span>
            </button>

            <!-- Fondo Oscuro cuando el modal está activo -->
            <div id="modalOverlay"
                style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1500;"
                onclick="cerrarModalCarrito()">
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    actualizarCarritoAgregar();
                    actualizarContadorAgregar();
                });

                function actualizarContadorAgregar() {
                    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

                    let contadorProductos = document.getElementById("contadorProductoAgregar");
                    let contadorModal = document.getElementById("contadorProductoAgregarModal");

                    if (!contadorProductos && !contadorModal) {
                        console.warn("⚠ Advertencia: No se encontraron los contadores del carrito. No se actualizará nada.");
                        return;
                    }

                    let totalProductos = carrito.reduce((total, producto) => total + producto.cantidad, 0);

                    if (contadorProductos) contadorProductos.innerText = totalProductos;
                    if (contadorModal) contadorModal.innerText = totalProductos;

                    console.log("✅ Contador actualizado:", totalProductos);
                }

                document.addEventListener("DOMContentLoaded", () => {
                    let botonToggle = document.getElementById("toggleAsideAgregarModal");

                    if (!botonToggle) {
                        console.error("❌ Error: El botón para abrir el modal no se encontró en el DOM");
                        return;
                    }

                    botonToggle.addEventListener("click", () => {
                        console.log("🛒 Botón de carrito clickeado, abriendo modal...");
                        mostrarModalCarrito();
                    });
                });

                function mostrarModalCarrito() {
                    console.log("✅ Intentando abrir el modal...");

                    let modal = document.getElementById("modalCarrito");
                    let overlay = document.getElementById("modalOverlay");

                    if (!modal || !overlay) {
                        console.error("❌ Error: No se encontró el modal o el overlay");
                        return;
                    }

                    modal.style.display = "block";
                    overlay.style.display = "block";

                    actualizarModalCarrito(); // Asegurar que se actualicen los datos

                    console.log("✅ Modal abierto correctamente");
                }

                function cerrarModalCarrito() {
                    document.getElementById("modalCarrito").style.display = "none";
                    document.getElementById("modalOverlay").style.display = "none";
                }

                function actualizarModalCarrito() {
                    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
                    console.log("🛒 Contenido del carrito:", carrito); // 🛠 Depuración

                    let carritoListaModal = document.getElementById("carritoListaModal");
                    let totalPedidoModal = document.getElementById("totalPedidoModal");
                    let contadorProductoAgregarModal = document.getElementById("contadorProductoAgregarModal");

                    if (!carritoListaModal || !totalPedidoModal || !contadorProductoAgregarModal) {
                        console.error("❌ Error: No se encontraron elementos del carrito modal");
                        return;
                    }

                    carritoListaModal.innerHTML = "";
                    let total = 0;
                    let totalProductos = 0;

                    carrito.forEach((producto, index) => {
                        console.log(`📌 Producto ${index + 1}:`, producto);

                        let div = document.createElement("div");
                        div.innerHTML = `<div class="flex items-center bg-gray-100 p-2 rounded-lg shadow-md w-full gap-3">
                            <!-- 📸 IMAGEN DEL PRODUCTO -->
                            <div class="w-12 h-12 flex-shrink-0 overflow-hidden rounded-md border border-gray-300">
                                ${
                                    producto.imagenUrl 
                                    ? `<img src="${producto.imagenUrl}" alt="${producto.nombre}" class="w-full h-full object-cover">`
                                    : `<div class="w-full h-full bg-gray-300 flex items-center justify-center"><span class="text-gray-600 text-xs">Sin Imagen</span></div>`
                                }
                            </div>

                            <!-- 📜 INFO DEL PRODUCTO -->
                            <div class="flex flex-col flex-grow">
                                <span class="text-sm font-semibold text-gray-800">${producto.nombre}</span>
                                <span class="text-md text-blue-600 font-bold">S/${producto.precio.toFixed(2)}</span>
                            </div>

                            <!-- 🔘 CONTROLES DEL PRODUCTO -->
                            <div class="flex items-center gap-2">
                                <button onclick="cambiarCantidad(${index}, -1)" class="px-2 py-1 text-white rounded-lg">➖</button>
                                <span class="px-3 py-1 bg-white border border-gray-300 rounded-md">${producto.cantidad}</span>
                                <button onclick="cambiarCantidad(${index}, 1)" class="px-2 py-1 text-white rounded-lg">➕</button>
                                <button onclick="eliminarDelCarrito(${index})" class="px-2 py-1 text-white rounded-lg ">❌</button>
                            </div>
                        </div>`;
                        carritoListaModal.appendChild(div);
                        total += producto.precio * producto.cantidad;
                        totalProductos += producto.cantidad;
                    });

                    totalPedidoModal.innerText = total.toFixed(2);
                    contadorProductoAgregarModal.innerText = totalProductos;

                    // Copiar datos del formulario original al modal
                    let nombreInput = document.getElementById("nombre");
                    let correoInput = document.getElementById("correo");
                    let nombreModalInput = document.getElementById("nombreModal");
                    let correoModalInput = document.getElementById("correoModal");

                    if (nombreInput && correoInput && nombreModalInput && correoModalInput) {
                        nombreModalInput.value = nombreInput.value;
                        correoModalInput.value = correoInput.value;
                    } else {
                        console.error("❌ Error: No se encontraron los campos de formulario");
                    }
                }

                function mostrarModalCarrito() {
                    console.log("Intentando abrir el modal...");

                    let modal = document.getElementById("modalCarrito");
                    let overlay = document.getElementById("modalOverlay");

                    if (!modal || !overlay) {
                        console.error("❌ Error: No se encontró el modal o el overlay");
                        return;
                    }

                    modal.style.display = "block";
                    overlay.style.display = "block";
                    actualizarModalCarrito(); // Asegurar que se actualicen los datos

                    console.log("✅ Modal abierto correctamente");
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
                            cerrarModalCarrito(); // 🔥 Cierra el modal antes de la alerta
                            setTimeout(() => {
                                Swal.fire("¡Éxito!", "Pedido realizado con éxito.", "success").then(() => {
                                    localStorage.removeItem("carrito");
                                    actualizarContadorAgregar();
                                    window.location.href = response.data.redirect;
                                });
                            }, 300); // 🔥 Pequeño retraso para que el modal se cierre antes
                        })
                        .catch(error => {
                            console.error(error);
                            Swal.fire("Error", "Error al realizar el pedido: " + (error.response?.data?.error ||
                                "Error desconocido"), "error");
                        });
                }

                document.addEventListener("DOMContentLoaded", () => {
                    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

                    // Asegurar que carrito es un array válido
                    if (!Array.isArray(carrito)) {
                        carrito = [];
                        localStorage.setItem("carrito", JSON.stringify(carrito));
                    }

                    actualizarCarritoAgregar(); // Muestra el carrito al cargar la página
                });

                function agregarAlCarrito(id, nombre, precio, imagenUrl) {
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
                            imagenUrl
                        });
                    }

                    localStorage.setItem("carrito", JSON.stringify(carrito));
                    actualizarCarritoAgregar();
                    actualizarContadorAgregar();
                }

                function actualizarCarritoAgregar() {
                    let carritoLista = document.getElementById("carritoLista");
                    let totalPedido = document.getElementById("totalPedido");

                    if (!carritoLista || !totalPedido) return; // ✅ Si no existen, salimos de la función

                    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
                    carritoLista.innerHTML = "";
                    let total = 0;

                    carrito.forEach((producto, index) => {
                        let div = document.createElement("div");
                        div.innerHTML = `<div class="flex items-center bg-gray-100 p-2 rounded-lg shadow-md w-full gap-3">
                                        <!-- 📸 IMAGEN DEL PRODUCTO -->
                                        <div class="w-12 h-12 flex-shrink-0 overflow-hidden rounded-md border border-gray-300">
                                            ${
                                                producto.imagenUrl 
                                                ? `<img src="${producto.imagenUrl}" alt="${producto.nombre}" class="w-full h-full object-cover">`
                                                : `<div class="w-full h-full bg-gray-300 flex items-center justify-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span class="text-gray-600 text-xs">Sin Imagen</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>`
                                            }
                                        </div>
                                        
                                        <!-- 📜 INFO DEL PRODUCTO -->
                                        <div class="flex flex-col flex-grow">
                                            <span class="text-sm font-semibold text-gray-800">${producto.nombre}</span>
                                            <span class="text-md text-blue-600 font-bold">S/${producto.precio.toFixed(2)}</span>
                                        </div>
                                        
                                        <!-- 🔘 CONTROLES DEL PRODUCTO -->
                                        <div class="flex items-center">
                                            
                                            <button onclick="cambiarCantidad(${index}, -1)" class="px-2 py-1 text-white rounded-lg">➖</button>
                                            <span class="px-3 py-1 bg-white border border-gray-300 rounded-md">${producto.cantidad}</span>
                                            <button onclick="cambiarCantidad(${index}, 1)" class="px-2 py-1 text-white rounded-lg">➕</button>
                                            <button onclick="eliminarDelCarrito(${index})" class="px-2 py-1 text-white rounded-lg">❌</button>
                                        </div>
                                    </div>`;


                        carritoLista.appendChild(div);
                        total += producto.precio * producto.cantidad;
                    });

                    totalPedido.innerText = total.toFixed(2);
                    actualizarContadorAgregar();
                }

                function cambiarCantidad(index, cambio) {
                    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
                    if (carrito[index]) {
                        carrito[index].cantidad += cambio;
                        if (carrito[index].cantidad < 1) carrito[index].cantidad = 1;
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
