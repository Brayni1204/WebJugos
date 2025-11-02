<x-app-layout>
    <div style="height: 100vh; overflow: auto;">
        <div class="mb-10 pb-10">
            <div>
                <div>
                    <div>
                        @php
                            $empresa = App\Models\Empresa::find(1);
                        @endphp
                    </div>
                </div>
                <div>
                    <div class="py-4">
                        <div class="container mx-auto px-4">
                            <nav class="flex items-center space-x-4 text-gray-600" style="justify-content: center">
                                <a href="/" class="text-gray-500 hover:text-gray-800 font-medium">Home</a>
                                <span>/</span>
                                <div class="flex">
                                    <a href="" class="text-gray-800 font-semibold">Carrito</a>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="container mx-auto px-4 py-8">
                    @if ($items != null)
                        <div class="flex justify-between text-center items-center mb-2 p-1">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800">Carrito de Compras</h2>
                            </div>
                            <div>
                                <button onclick="confirmarVaciarCarrito()"
                                    class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 transition duration-300">
                                    Vaciar Carrito
                                </button>
                                <form id="vaciarCarritoForm" action="{{ route('pagecarrito.vaciar') }}" method="POST">
                                    @csrf
                                </form>

                            </div>
                        </div>
                        @if (session('success'))
                            <div class="mb-4 p-4 bg-green-500 text-white rounded">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div>
                            <div class="overflow-auto">
                                <table class="w-full border-collapse border border-gray-200">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="border border-gray-300 p-2">Producto</th>
                                            <th class="border border-gray-300 p-2">Cantidad</th>
                                            <th class="border border-gray-300 p-2">Precio</th>
                                            <th class="border border-gray-300 p-2">Total</th>
                                            <th class="border border-gray-300 p-2">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $subtotal = 0;
                                            $costoDelivery = 5.0;
                                        @endphp

                                        @foreach ($items as $item)
                                            @php
                                                $subtotal += ($item->price ?? 0) * $item->qty;
                                            @endphp
                                            <tr class="border border-gray-200">
                                                <td class="p-2">{{ $item->name }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm flex justify-center gap-4 text-xl"
                                                        role="group" aria-label="Small button group">
                                                        <a href="{{ route('carrito.decrementarcantidad', $item->getHash()) }}"
                                                            class="bg-gray-300 text-white px-3 py-1 rounded hover:bg-gray-400 transition duration-300">
                                                            ➖
                                                        </a>
                                                        <span
                                                            class="px-3 py-1 border-gray-300 rounded">{{ $item->qty }}</span>
                                                        <a href="{{ route('carrito.incrementarcantidad', $item->getHash()) }}"
                                                            class="bg-gray-300 text-white px-3 py-1 rounded hover:bg-gray-400 transition duration-300">
                                                            ➕
                                                        </a>
                                                    </div>
                                                </td>
                                                <td class="p-2">
                                                    S/. {{ number_format($item->price ?? 0, 2) }}
                                                </td>
                                                <td class="p-2">
                                                    S/. {{ number_format(($item->price ?? 0) * $item->qty, 2) }}
                                                </td>
                                                <td class="p-2" width="150px">
                                                    <form action="{{ route('carrito.eliminar') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="rowId"
                                                            value="{{ $item->getHash() }}">
                                                        <div class="flex justify-center">
                                                            <button type="submit"
                                                                class="bg-red-300 text-white px-2 py-1 rounded hover:bg-red-600 transition duration-300">
                                                                ❌
                                                            </button>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr> 
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div>

                                <!-- ✅ Resumen del Pedido -->
                                <div class="mt-6 p-4 bg-white shadow-md rounded-lg">
                                    <h2 class="text-2xl font-bold text-gray-800">Resumen del Pedido</h2>
                                    <div class="flex justify-between mt-2 text-lg">
                                        <span>Subtotal:</span>
                                        <span class="font-semibold">S/. {{ number_format($subtotal, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between mt-2 text-lg">
                                        <span>Costo de Delivery:</span>
                                        <span id="costoDelivery" class="font-semibold">S/. 0.00</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="flex justify-between text-xl font-bold">
                                        <span>Total a Pagar:</span>
                                        <span id="totalPago" class="text-blue-600">S/.
                                            {{ number_format($subtotal, 2) }}</span>
                                    </div>
                                </div>

                                <!-- ✅ Formulario para realizar el pedido -->
                                <form id="pedidoForm" method="POST" action="{{ route('pedido.realizar') }}">
                                    @csrf

                                    <div>
                                        <!-- Datos ocultos de los productos -->
                                        @foreach ($items as $item)
                                            <input type="hidden" name="productos[{{ $loop->index }}][nombre]"
                                                value="{{ $item->name }}">
                                            <input type="hidden" name="productos[{{ $loop->index }}][cantidad]"
                                                value="{{ $item->qty }}">
                                            <input type="hidden" name="productos[{{ $loop->index }}][precio]"
                                                value="{{ $item->price }}">
                                        @endforeach

                                        <input type="hidden" name="subtotal" value="{{ $subtotal }}">
                                        <input type="hidden" name="costo_delivery" id="costo_delivery_input"
                                            value="0">
                                        <input type="hidden" name="total_pago" id="total_pago_input"
                                            value="{{ $subtotal }}">
                                    </div>

                                    <!-- Datos del Cliente -->
                                    <div class="mt-6 p-4 bg-white shadow-md rounded-lg">
                                        <h2 class="text-lg font-bold text-gray-800">Información del Cliente</h2>
                                        <div class="flex flex-wrap md:flex-nowrap gap-4">
                                            <div class="w-full md:w-1/2">
                                                <label for="nombre" class="text-gray-500 text-lg">Nombre:</label>
                                                <input type="text" name="nombre" id="nombre" required
                                                    class="w-full p-2 border rounded" autocomplete="off">

                                                <label for="apellidos" class="text-gray-500 text-lg">Apellidos:</label>
                                                <input type="text" name="apellidos" id="apellidos" required
                                                    class="w-full p-2 border rounded" autocomplete="off">
                                            </div>
                                            <div class="w-full md:w-1/2">
                                                <label for="email" class="text-gray-500 text-lg">Correo
                                                    Electrónico:</label>
                                                <input type="email" name="email" id="email" required
                                                    class="w-full p-2 border rounded" autocomplete="off"
                                                    placeholder="ejemplo@correo.com">

                                                <label for="telefono" class="text-gray-500 text-lg">Teléfono:</label>
                                                <input type="tel" name="telefono" id="telefono" required
                                                    class="w-full p-2 border rounded" autocomplete="off"
                                                    placeholder="987654321" maxlength="9" pattern="[0-9]{9}"
                                                    title="Ingrese un número de 9 dígitos"
                                                    oninput="this.value = this.value.replace(/\D/g, '')">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Método de Entrega -->
                                    <div class="mt-6">
                                        <h2 class="text-xl font-bold text-gray-800">Método de Entrega</h2>
                                        <div class="flex gap-6">
                                            <label>
                                                <input type="radio" name="metodoEntrega" value="local" checked>
                                                Recojo
                                                en
                                                Local
                                            </label>
                                            <label>
                                                <input type="radio" name="metodoEntrega" value="delivery"> Delivery
                                                a
                                                domicilio
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Dirección (Solo si es Delivery) -->
                                    <div id="direccionForm" class="hidden mt-6 p-4 bg-white shadow-md rounded-lg">
                                        <div class="flex justify-center flex-col text-center">
                                            <h1 class="text-lg text-gray-800 font-semibold">
                                                Direccion del pedido
                                            </h1>
                                        </div>
                                        <div>
                                            <p class="text-lg text-gray-500">
                                                Completa los campos
                                            </p>
                                            <div class="flex flex-col bg-gray-100 p-4 w-full">
                                                <div class="flex flex-wrap md:flex-nowrap gap-4">
                                                    <div class="w-full">
                                                        <label for="departamento"
                                                            class="text-gray-500 text-lg">Departamento:</label>
                                                        <input type="text" name="departamento" id="departamento"
                                                            value="{{ $empresa->departamento }}"
                                                            class="w-full p-2 border rounded" autocomplete="off"
                                                            readonly>
                                                        <div class="flex flex-wrap md:flex-nowrap gap-2">
                                                            <div class="w-full md:w-1/2">
                                                                <label for="provincia"
                                                                    class="text-gray-500 text-lg">Provincia:</label>
                                                                <input type="text" name="provincia" id="provincia"
                                                                    value="{{ $empresa->provincia }}"
                                                                    class="w-full p-2 border rounded"
                                                                    autocomplete="off" readonly>
                                                            </div>
                                                            <div class="w-full md:w-1/2">
                                                                <label for="distrito"
                                                                    class="text-gray-500 text-lg">Distrito:</label>
                                                                <input type="text" name="distrito" id="distrito"
                                                                    value="{{ $empresa->provincia }}"
                                                                    class="w-full p-2 border rounded"
                                                                    autocomplete="off" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="flex flex-wrap md:flex-nowrap gap-2">
                                                            <div class="w-full md:w-3/5">
                                                                <label for="calle">Calle:</label>
                                                                <input type="text" name="calle" id="calle"
                                                                    class="w-full p-2 border rounded"
                                                                    autocomplete="off">
                                                            </div>
                                                            <div class="w-full md:w-3/5">
                                                                <label for="numero">Número:</label>
                                                                <input type="text" name="numero" id="numero"
                                                                    class="w-full p-2 border rounded"
                                                                    autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div>
                                        @auth
                                            <button type="submit"
                                                class="bg-blue-500 text-white px-4 py-2 rounded mt-4 w-full">Realizar
                                                Pedido</button>
                                        @else
                                            <div
                                                class="flex flex-col items-center justify-center p-4 bg-white shadow-md rounded-lg mt-10">
                                                <p class="text-lg text-gray-800 font-semibold mb-4">
                                                    Para realizar el pedido, primero inicia sesión o regístrate:
                                                </p>
                                                <div class="flex space-x-4">
                                                    <a href="{{ route('login') }}"
                                                        class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 transition duration-300">
                                                        Iniciar Sesión
                                                    </a>
                                                    <a href="{{ route('register') }}"
                                                        class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-600 transition duration-300">
                                                        Registrarse
                                                    </a>
                                                </div>
                                            </div>
                                        @endauth
                                    </div>
                                </form>

                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        const metodoEntregaRadios = document.querySelectorAll('input[name="metodoEntrega"]');
                                        const direccionForm = document.getElementById('direccionForm');
                                        const calleInput = document.getElementById('calle');
                                        const numeroInput = document.getElementById('numero');
                                        const costoDeliverySpan = document.getElementById('costoDelivery');
                                        const totalPagoSpan = document.getElementById('totalPago');
                                        const departamentoInput = document.getElementById('departamento');
                                        const provinciaInput = document.getElementById('provincia');
                                        const distritoInput = document.getElementById('distrito');

                                        const costoDelivery = {{ $empresa->delivery ?? 0 }};
                                        const subtotal = {{ $subtotal ?? 0 }};

                                        function actualizarResumen() {
                                            const metodoSeleccionado = document.querySelector('input[name="metodoEntrega"]:checked').value;

                                            if (metodoSeleccionado === 'delivery') {
                                                direccionForm.classList.remove('hidden');
                                                departamentoInput.setAttribute("required", "true");
                                                provinciaInput.setAttribute("required", "true");
                                                distritoInput.setAttribute("required", "true");
                                                calleInput.setAttribute("required", "true");
                                                numeroInput.setAttribute("required", "true");

                                                // Mostrar costo de delivery
                                                costoDeliverySpan.textContent = `S/. ${costoDelivery.toFixed(2)}`;
                                                totalPagoSpan.textContent = `S/. ${(subtotal + costoDelivery).toFixed(2)}`;

                                                // ✅ Actualizar los campos ocultos en el formulario
                                                document.getElementById('costo_delivery_input').value = costoDelivery.toFixed(2);
                                                document.getElementById('total_pago_input').value = (subtotal + costoDelivery).toFixed(2);
                                            } else {
                                                direccionForm.classList.add('hidden');
                                                departamentoInput.removeAttribute("required");
                                                provinciaInput.removeAttribute("required");
                                                distritoInput.removeAttribute("required");
                                                calleInput.removeAttribute("required");
                                                numeroInput.removeAttribute("required");

                                                // Ocultar costo de delivery
                                                costoDeliverySpan.textContent = 'S/. 0.00';
                                                totalPagoSpan.textContent = `S/. ${subtotal.toFixed(2)}`;

                                                // ✅ Si es "Recojo en Local", asegurarse de enviar costo_delivery = 0
                                                document.getElementById('costo_delivery_input').value = "0.00";
                                                document.getElementById('total_pago_input').value = subtotal.toFixed(2);
                                            }
                                        }

                                        // Agregar evento de cambio a los radio buttons
                                        metodoEntregaRadios.forEach(radio => {
                                            radio.addEventListener("change", actualizarResumen);
                                        });

                                        // Llamar la función al cargar la página
                                        actualizarResumen();
                                    });
                                </script>

                            </div>

                        </div>
                    @else
                        <div class="text-center">
                            <p class="text-gray-500 text-lg">El carrito está vacío.</p>
                        </div>
                    @endif
                </div>
            </div>
            <div>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    function confirmarEliminacion(rowId) {
                        Swal.fire({
                            title: "¿Estás seguro?",
                            text: "Este producto será eliminado del carrito.",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#d33",
                            cancelButtonColor: "#3085d6",
                            confirmButtonText: "Sí, eliminar",
                            cancelButtonText: "Cancelar"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                eliminarProducto(rowId);
                            }
                        });
                    }

                    function eliminarProducto(rowId) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = "{{ route('pagecarrito.eliminar') }}";
                        form.innerHTML = `
                        @csrf
                        <input type="hidden" name="rowId" value="${rowId}">
                    `;
                        document.body.appendChild(form);
                        form.submit();
                    }

                    function confirmarVaciarCarrito() {
                        Swal.fire({
                            title: "¿Vaciar carrito?",
                            text: "Esta acción eliminará todos los productos del carrito.",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#d33",
                            cancelButtonColor: "#3085d6",
                            confirmButtonText: "Sí, vaciar",
                            cancelButtonText: "Cancelar"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById("vaciarCarritoForm").submit();
                            }
                        });
                    }
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
