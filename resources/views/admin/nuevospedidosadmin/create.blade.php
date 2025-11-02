@extends('adminlte::page')

@section('title', 'Crear Pedido')

@section('content_header')

@stop
@section('content')
    <div class="card">
        <div class="card-header">
            <div style="display: flex; justify-content: center">
                <h1>Nuevo Pedido</h1>
            </div>
        </div>
        <div class="card-body px-0" style="height: 85vh">
            <div class="container-fluid p-0" style="display: flex; justify-content: center;">
                <div class="container m-0">
                    <div class="p-11" style="padding-bottom: 50px;">
                        <form action="" method="post">
                            @csrf
                            <div class="p-2">
                                <label for="mesa" class="form-label">Mesas Disponibles</label>
                                <select name="mesa" id="mesa" class="form-control">
                                    <option value="mesa"> Selecciona una mesa</option>
                                    @foreach ($mesasdisponibles as $item)
                                        <option value="{{ $item->id }}">Mesa N° - {{ $item->numero_mesa }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <div class="row">
                                    <div class="col-md-8 border border-dark p-1">
                                        <div
                                            class="d-flex align-items-center flex-wrap gap-3 p-3 bg-light rounded shadow-sm">
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
                                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
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
                                    <div
                                        class="col-md-4 border border-dark p-2"style="justify-content: space-around; display: flex;flex-direction: column;">
                                        <div style="display: flex; justify-content: center">
                                            <h5>Detalle del Producto</h5>
                                        </div>
                                        <div id="detalle-producto"
                                            class="border p-1 bg-light flex justify-center text-center"
                                            style="height: 300px; overflow-y: auto;">
                                            <p class="text-muted">Seleccione un producto para ver los detalles aquí.</p>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-primary" id="agregar-producto"
                                                style="display: none; width: 100%" onclick="crearPedido()">Agregar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="caracteristicas-container">
                                <label><input type="checkbox" name="caracteristicas[]" class="grupo-temperatura"
                                        value="helado"> Helado</label>
                                <label><input type="checkbox" name="caracteristicas[]" class="grupo-temperatura"
                                        value="temperado"> Temperado</label>
                                <label><input type="checkbox" name="caracteristicas[]" class="grupo-temperatura"
                                        value="temperatura ambiente"> Temperatura ambiente</label>

                                <label><input type="checkbox" name="caracteristicas[]" class="grupo-azucar"
                                        value="con azúcar"> Con azúcar</label>
                                <label><input type="checkbox" name="caracteristicas[]" class="grupo-azucar"
                                        value="sin azúcar"> Sin azúcar</label>
                                <label><input type="checkbox" name="caracteristicas[]" class="grupo-azucar"
                                        value="bajo en azúcar"> Bajo en azúcar</label>
                            </div>



                            <!-- Método de Entrega -->
                            <div class="mt-3">
                                <label for="metodo_entrega" class="form-label">Método de Entrega</label>
                                <select name="metodo_entrega" id="metodo_entrega" class="form-control">
                                    <option value="mesa">Mesa</option>
                                </select>
                            </div>

                            <div class="floating-btn-container">
                                <!-- 🔙 Botón para Regresar -->
                                <a href="{{ route('admin.nuevospedidosadmin.index') }}" class="floating-btn back-btn"
                                    title="Regresar">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
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

    <script>
        document.querySelector('#mesa').addEventListener('change', function() {
            const mesaSeleccionada = this.value;
            document.querySelector('#agregar-producto').disabled = (mesaSeleccionada === "mesa");
        });
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
                    <p class="m-0"><strong>Nombre:</strong> ${nombre}</p>
                    <p class="m-0"><strong>Precio:</strong> $${precio.toFixed(2)}</p>
                    ${imagen ? `<img src="${imagen}" alt="Imagen de ${nombre}" style="max-width: 50%; height: auto;">` : '<p class="text-muted">Sin imagen disponible</p>'}
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
        function crearPedido() {

            const mesa = document.querySelector('#mesa').value;

            if (mesa === "mesa") {
                Swal.fire("Error", "Por favor, selecciona una mesa antes de continuar", "error");
                return;
            }

            if (!productoSeleccionado) {
                Swal.fire("Error", "Selecciona un producto antes de continuar", "error");
                return;
            }

            let caracteristicasSeleccionadas = Array.from(document.querySelectorAll(
                    'input[name="caracteristicas[]"]:checked'))
                .map(cb => cb.value);

            let formData = {
                mesa: mesa,
                metodo_entrega: document.querySelector('#metodo_entrega').value,
                producto_id: productoSeleccionado.id,
                cantidad: 1,
                caracteristicas: caracteristicasSeleccionadas,
                _token: "{{ csrf_token() }}"
            };


            fetch("{{ route('admin.nuevospedidosadmin.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": formData._token
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire("Éxito", "Pedido creado correctamente", "success")
                            .then(() => {
                                window.location.href = data.redirect;
                            });
                    } else {
                        Swal.fire("Error", data.error, "error");
                    }
                })
                .catch(error => {
                    console.error("❌ Error en la solicitud fetch:", error);
                    Swal.fire("Error", "Hubo un problema al crear el pedido", "error");
                });
        }
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
