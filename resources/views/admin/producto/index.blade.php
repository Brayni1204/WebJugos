@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
    <h1>Lista de Productos</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">
            <div class="container-fluid">
                <div class="mb-3 row align-items-center">
                    <!-- Botón Nuevo Producto -->
                    <div class="col-12 col-md-auto mb-2 mb-md-0">
                        <a href="{{ route('admin.producto.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus"></i> Nuevo Producto
                        </a>
                    </div>
 
                    <!-- Formulario de Búsqueda -->
                    <div class="col-12 col-md-auto">
                        <form action="{{ route('admin.producto.index') }}" method="GET" class="d-flex flex-nowrap">
                            <input type="text" name="search" class="form-control" placeholder="Buscar producto..."
                                value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary ml-2">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>


                <!-- Tabla Responsiva -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="text-center">
                            <tr>
                                <th>Código</th>
                                <th>Imagen</th>
                                <th>Producto</th>
                                <th>Precio Venta</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($productos as $producto)
                                <tr>
                                    <td>{{ $producto->id }}</td>
                                    <td>
                                        @if ($producto->image && $producto->image->isNotEmpty())
                                            <img src="{{ Storage::url($producto->image->first()->url) }}"
                                                class="img-thumbnail shadow-sm" width="50">
                                        @else
                                            <i class="fas fa-image-slash text-muted"> Sin imagen</i>
                                        @endif
                                    </td>
                                    <td>{{ $producto->nombre_producto }}</td>
                                    <td>S/. {{ $producto->precios->precio_venta ?? '0.00' }}</td>
                                    <td>
                                        <span class="badge {{ $producto->status == 1 ? 'badge-success' : 'badge-danger' }}">
                                            {{ $producto->status == 1 ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.producto.show', $producto) }}"
                                                class="btn btn-sm text-danger">
                                                <i class="fas fa-eye fa-lg"></i>
                                            </a>
                                            <a href="{{ route('admin.producto.edit', $producto) }}"
                                                class="btn btn-sm text-primary">
                                                <i class="fas fa-edit fa-lg"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm text-danger delete-product"
                                                data-url="{{ route('admin.producto.destroy', $producto) }}">
                                                <i class="fas fa-times fa-lg"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $productos->appends(['search' => request('search')])->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

        <!-- Botones flotantes -->
        <div class="floating-btn-container">
            <a href="{{ route('admin.producto.create') }}" class="floating-btn" title="Agregar Producto">
                <i class="fas fa-plus"></i>
            </a>
            <a href="{{ route('admin.home') }}" class="floating-btn back-btn" title="Regresar">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* Botones flotantes */
        .floating-btn-container {
            position: fixed;
            bottom: 15px;
            right: 15px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .floating-btn {
            background-color: #007bff;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            transition: all 0.3s;
            text-decoration: none;
        }

        .floating-btn:hover {
            background-color: #0056b3;
            transform: scale(1.1);
        }

        .back-btn {
            background-color: #dc3545;
        }

        .back-btn:hover {
            background-color: #b02a37;
        }

        /* Estilos Responsivos */
        @media (max-width: 768px) {
            .floating-btn {
                width: 45px;
                height: 45px;
                font-size: 18px;
            }

            .table {
                font-size: 14px;
            }
        }

        @media (max-width: 576px) {
            .floating-btn-container {
                right: 10px;
                bottom: 10px;
            }

            .floating-btn {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.delete-product').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    let url = this.getAttribute("data-url");

                    Swal.fire({
                        title: "¿Eliminar este producto?",
                        text: "Esta acción no se puede deshacer.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Sí, eliminar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(url, {
                                    method: "DELETE",
                                    headers: {
                                        "X-CSRF-TOKEN": document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute(
                                            'content')
                                    }
                                }).then(response => response.json())
                                .then(data => {
                                    Swal.fire("¡Eliminado!",
                                        "El producto ha sido eliminado.", "success");
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1500);
                                })
                                .catch(error => {
                                    Swal.fire("Error",
                                        "Hubo un problema al eliminar el producto.",
                                        "error");
                                });
                        }
                    });
                });
            });
        });

        @if (session('success'))
            Swal.fire({
                title: "¡Éxito!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "OK"
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: "Error",
                text: "{{ session('error') }}",
                icon: "error",
                confirmButtonText: "OK"
            });
        @endif
    </script>
@stop
