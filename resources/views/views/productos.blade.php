<x-app-layout>
    <div>
        <div class="mb-10 mt-24">
            <div class="py-4">
                <nav class="flex justify-center items-center space-x-2 text-gray-600 text-sm sm:text-base">
                    <a href="/" class="text-gray-500 hover:text-gray-800 font-medium">Home</a>
                    <span>/</span>
                    <a href="" class="text-gray-800 font-semibold">Nuestra Carta</a>
                </nav>
            </div>
            <div class="bg-white">
                <div>
                    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="flex items-baseline justify-center border-b border-gray-200 pt-6 pb-6">
                            <h1 class="text-4xl font-bold tracking-tight text-gray-900">Nuestra Carta</h1>
                        </div>
                        @php
                            $categoriaActual = request('categoria')
                                ? $categorias->where('id', request('categoria'))->first()
                                : null;
                            $nombreCategoria = $categoriaActual
                                ? $categoriaActual->nombre_categoria
                                : 'Todas las categorías';

                            // Aquí puedes definir prefijos personalizados para ciertas categorías (ajústalos según necesites)
                            $prefijos = [
                                'Jugos' => ['Jugo de Naranja', 'Jugo de Fresa'], // Ejemplo de nombres que caen en "Jugos"
                                'Postres' => ['Torta', 'Helado'],
                                'Comidas' => ['Pollo a la Brasa', 'Ceviche'],
                            ];

                            $prefijoSeleccionado = 'Categoría'; // Valor por defecto

                            foreach ($prefijos as $clave => $nombres) {
                                if (in_array($nombreCategoria, $nombres)) {
                                    $prefijoSeleccionado = $clave;
                                    break;
                                }
                            }
                        @endphp

                        <span class="text-lg font-bold text-blue-600">
                            {{ $prefijoSeleccionado }} - {{ $nombreCategoria }}
                        </span>


                        <section aria-labelledby="products-heading" class="pt-6 pb-24">
                            <h2 id="products-heading" class="sr-only">Products</h2>




                            <div class="grid grid-cols-1 gap-x-8 gap-y-10 lg:grid-cols-4">
                                <!-- Product grid -->

                                <div class="lg:col-span-3">
                                    <div
                                        class="lg:col-span-3 grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-1 sm:gap-6">
                                        @foreach ($productos as $producto)
                                            <div
                                                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 flex flex-col">
                                                <!-- Imagen del producto -->
                                                @if ($producto->image->isNotEmpty())
                                                    <img src="{{ asset('storage/' . $producto->image->first()->url) }}"
                                                        alt="{{ $producto->nombre_producto }}"
                                                        class="w-full h-36 object-cover">
                                                @else
                                                    <div
                                                        class="h-48 bg-gray-300 flex items-center justify-center text-gray-500">
                                                        <span>Imagen No Disponible</span>
                                                    </div>
                                                @endif

                                                <!-- Contenido del producto -->
                                                <div class="p-4 flex flex-col flex-grow">
                                                    <div class="flex justify-center">
                                                        <h2 class="text-lg font-semibold text-gray-800">
                                                            {{ $producto->nombre_producto }}</h2>
                                                    </div>
                                                    <p class="text-gray-600 text-sm">{{ $producto->descripcion }}
                                                    </p>

                                                    <!-- Precio -->
                                                    @if ($producto->precios)
                                                        <div class="flex justify-center">
                                                            <p class="text-lg font-bold text-blue-500 mt-2">S/.
                                                                {{ number_format($producto->precios->precio_venta, 2) }}
                                                            </p>
                                                        </div>
                                                    @else
                                                        <p class="text-lg font-bold text-gray-500 mt-2">Precio no
                                                            disponible
                                                        </p>
                                                    @endif

                                                    @php
                                                        $imageUrl = $producto->image->isNotEmpty()
                                                            ? asset('storage/' . $producto->image->first()->url)
                                                            : '';
                                                    @endphp
                                                    <div class="mt-auto flex justify-center">
                                                        <button type="button"
                                                            onclick="mostrarOpcionesProducto({{ $producto->id }}, '{{ $producto->nombre_producto }}', {{ $producto->precios->precio_venta ?? 0 }}, '{{ $imageUrl }}')"
                                                            class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                                                            Agregar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- Filters -->
                                <ul role="list"
                                    class="hidden sm:block space-y-4 border-b border-gray-200 pb-6 text-sm font-medium text-gray-900 max-h-52 overflow-y-auto sm:max-h-none sm:overflow-visible">
                                    <!-- Opción para ver todas las categorías -->
                                    <li>
                                        <a href="{{ route('views.productos') }}"
                                            class="block px-2 py-3 {{ request('categoria') ? 'text-gray-500' : 'text-blue-600 font-bold' }}">
                                            Todas las categorías
                                        </a>
                                    </li>

                                    @foreach ($categorias as $cat)
                                        <li>
                                            <a href="{{ route('views.productos', ['categoria' => $cat->id]) }}"
                                                class="block px-2 py-3 {{ request('categoria') == $cat->id ? 'text-blue-600 font-bold' : 'text-gray-500' }}">
                                                {{ $cat->nombre_categoria }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                            </div>
                            <div class="mt-8">
                                {{ $productos->appends(request()->query())->links('pagination::tailwind') }}
                            </div>
                        </section>
                    </main>
                </div>
            </div>

            <div class="floating-btn-container relative sm:hidden">
                <!-- Menú de categorías -->
                <div id="categoriasLista"
                    class="hidden absolute bottom-20 right-0 bg-white shadow-lg border border-gray-300 rounded-lg p-2 w-48">
                    <ul>
                        <li>
                            <a href="{{ route('views.productos') }}"
                                class="block px-2 py-3 {{ request('categoria') ? 'text-gray-500' : 'text-blue-600 font-bold' }}">
                                Todas las categorías
                            </a>
                        </li>
                        @foreach ($categorias as $cat)
                            <li>
                                <a href="{{ route('views.productos', ['categoria' => $cat->id]) }}"
                                    class="block px-2 py-3 text-gray-600 hover:text-blue-600 font-medium">
                                    {{ $cat->nombre_categoria }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Botón flotante -->
                <a href="#" id="toggleCategorias"
                    class="floating-btn fixed bottom-12 right-4 bg-blue-600 text-white p-3 rounded-full shadow-lg border border-gray-300">
                    <i class="fas fa-plus"></i>
                </a>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const toggleBtn = document.getElementById('toggleCategorias');
                    const categoriasLista = document.getElementById('categoriasLista');

                    toggleBtn.addEventListener('click', (event) => {
                        event.preventDefault();
                        categoriasLista.classList.toggle('hidden');
                    });

                    document.addEventListener('click', (event) => {
                        if (!toggleBtn.contains(event.target) && !categoriasLista.contains(event.target)) {
                            categoriasLista.classList.add('hidden');
                        }
                    });
                });
            </script>


            <script>
                function openModal(button) {
                    const producto = JSON.parse(button.getAttribute('data-producto'));

                    document.getElementById('modal-title').innerText = producto.nombre_producto;
                    document.getElementById('modal-description').innerText = producto.descripcion;
                    document.getElementById('modal-price').innerText = 'Precio: $' + producto.precios.precio_venta;

                    const modalImage = document.getElementById('modal-image');
                    if (producto.image && producto.image.length > 0) {
                        modalImage.src = "{{ asset('storage') }}/" + producto.image[0].url;
                        modalImage.alt = producto.nombre_producto;
                    } else {
                        modalImage.src = '';
                        modalImage.alt = 'Imagen No Disponible';
                    }

                    const componentesList = document.getElementById('modal-componentes');
                    componentesList.innerHTML = '';
                    if (producto.componentes && producto.componentes.length > 0) {
                        producto.componentes.forEach(componente => {
                            const li = document.createElement('li');
                            li.innerText =
                                `${componente.nombre_componente} - ${componente.cantidad || 'Cantidad no especificada'}`;
                            componentesList.appendChild(li);
                        });
                    } else {
                        const li = document.createElement('li');
                        li.innerText = 'No hay componentes disponibles para este producto.';
                        componentesList.appendChild(li);
                    }

                    document.getElementById('product-modal').classList.remove('hidden');
                }

                function closeModal() {
                    document.getElementById('product-modal').classList.add('hidden');
                }
            </script>
            <style>
                #opcionesProductoModal,
                #opcionesProductoOverlay {
                    transition: opacity 0.3s ease-in-out;
                }

                #opcionesProductoModal {
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
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

                .floating-btn-container {
                    position: fixed;
                    bottom: 30px;
                    right: 0px;
                    display: grid;
                    gap: 10px;
                    align-items: center;
                }

                /* 🎨 Estilo General de Botones Flotantes */
                .floating-btn {
                    background-color: #007bff;
                    color: white;
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
        </div>
    </div>
    <!-- Modal para Opciones de Producto -->
    <div id="opcionesProductoOverlay" onclick="cerrarOpcionesProductoModal()"
        class="hidden fixed inset-0 bg-opacity-100 z-10"></div>

    <div id="opcionesProductoModal"
        class="hidden fixed w-11/12 max-w-md bg-white rounded-2xl shadow-xl z-50 p-6 max-h-[90vh] overflow-y-auto">
        <h3 id="opcionesProductoNombre" class="text-2xl font-bold text-gray-800 mb-2 text-center"></h3>
        <p class="text-center text-gray-500 mb-6">Puedes Agregar Sugerencias</p>

        <div class="space-y-5 w-80">
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
                        <input type="checkbox" name="caracteristicas_modal[]" value="temperado" class="hidden">
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
                        <input type="checkbox" name="caracteristicas_modal[]" value="con azúcar" class="hidden">
                        <span
                            class="block text-center p-2 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer transition-all duration-200">Normal</span>
                    </label>
                    <label class="flex-1">
                        <input type="checkbox" name="caracteristicas_modal[]" value="bajo en azúcar" class="hidden">
                        <span
                            class="block text-center p-2 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer transition-all duration-200">Bajo</span>
                    </label>
                    <label class="flex-1">
                        <input type="checkbox" name="caracteristicas_modal[]" value="sin azúcar" class="hidden">
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
            <button type="button" onclick="agregarAlCarritoDesdeModal()"
                class="flex-1 text-center py-2 px-4 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition-colors shadow-md shadow-blue-500/30">Añadir</button>
        </div>
    </div>

    <!-- Hidden form for adding to cart -->
    <form id="addToCartForm" action="{{ route('pagecarrito.agregar') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="id" id="form_producto_id">
        <input type="hidden" name="cantidad" id="form_producto_cantidad" value="1">
        <div id="form_producto_caracteristicas"></div>
    </form>

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

            document.getElementById('form_producto_id').value = productoParaAnadir.id;
            const caracteristicasContainer = document.getElementById('form_producto_caracteristicas');
            caracteristicasContainer.innerHTML = '';
            caracteristicasSeleccionadas.forEach(caracteristica => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'caracteristicas[]';
                input.value = caracteristica;
                caracteristicasContainer.appendChild(input);
            });

            document.getElementById('addToCartForm').submit();
        }

        document.addEventListener("DOMContentLoaded", () => {

            // Move modal to body to avoid stacking context issues

            const modal = document.getElementById('opcionesProductoModal');

            const overlay = document.getElementById('opcionesProductoOverlay');

            if (modal) document.body.appendChild(modal);

            if (overlay) document.body.appendChild(overlay);



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

                            sp.classList.remove('bg-blue-600', 'text-white', 'border-blue-500',
                                'shadow-md');

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
    </script>
</x-app-layout>
