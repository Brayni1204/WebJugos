@extends('adminlte::page')

@section('title', 'Categorias')

@section('content_header')
    <h1>Lista de Categorías</h1>
@stop

@section('content')
    @if (session('info'))
        <div class="alert alert-success">
            <strong>{{ session('info') }}</strong>
        </div>
    @endif

    <div class="card">

        <div class="card-header">
            <div class="mb-3 row align-items-center">
                <!-- Botón Nueva Categoría -->
                <div class="col-12 col-md-auto mb-2 mb-md-0">
                    <a href="{{ route('admin.categoria.create') }}" class="btn btn-primary w-100 md:w-auto">
                        <i class="fas fa-plus"></i> Nueva Categoría
                    </a>
                </div>

                <!-- Formulario de Búsqueda -->
                <div class="col-12 col-md-auto">
                    <form action="{{ route('admin.categoria.index') }}" method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control w-100 md:w-auto"
                            placeholder="Buscar categoría..." value="{{ request('search') }}">
                        <button type="submit" class="btn ml-1 btn-primary d-flex align-items-center">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>



        <div class="card-body" style="overflow: auto">
            <table class="table table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>Codigo</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($categorias as $categorium)
                        <tr>
                            <td>{{ $categorium->id }}</td>
                            <td>
                                @if ($categorium->image && $categorium->image->count())
                                    <img src="{{ Storage::url($categorium->image->first()->url) }}" alt="Img"
                                        width="50">
                                @else
                                    <small>Sin imagen</small>
                                @endif
                            </td>
                            <td>{{ $categorium->nombre_categoria }}</td>
                            <td>{{ $categorium->status_text }}</td>
                            <td width="100px">
                                <div class="d-flex justify-content-between gap-10">
                                    <a href="{{ route('admin.categoria.show', $categorium) }}" class="btn btn-sm">
                                        <i class="fas fa-eye fa-lg" style="color: red;"></i>
                                    </a>

                                    <a href="{{ route('admin.categoria.edit', $categorium) }}" class="btn btn-sm">
                                        <i class="fas fa-edit fa-lg" style="color: blue;"></i>
                                    </a>

                                    {{-- <form action="{{ route('admin.categoria.destroy', $categorium) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm delete-category">
                                            <i class="fas fa-times fa-lg" style="color: red;"></i>
                                        </button>
                                    </form> --}}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Agregar paginación -->
            <div class="d-flex justify-content-end" style="margin-top: 20px">
                {{ $categorias->appends(['search' => request('search')])->links('pagination::bootstrap-4') }}
            </div>
        </div>
        <div class="floating-btn-container">
            <!-- 🔹 Botón para Agregar Subtítulo -->
            <a href="{{ route('admin.categoria.create') }}" class="floating-btn" title="Agregar Categoria">
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
    <style>
        .floating-btn-container {
            position: fixed;
            bottom: 10px;
            right: 0px;
            display: grid;
            gap: 10px;
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Mostrar alertas de éxito o error
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

        // Confirmar eliminación de categoría
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.delete-category').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    let form = this.closest("form");

                    Swal.fire({
                        title: "¿Eliminar esta categoría?",
                        text: "Esta acción no se puede deshacer.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Sí, eliminar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(form.action, {
                                    method: "DELETE",
                                    headers: {
                                        "X-CSRF-TOKEN": document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute(
                                            'content')
                                    }
                                }).then(response => response.json())
                                .then(data => {
                                    Swal.fire("¡Eliminado!",
                                        "La categoría ha sido eliminada.", "success"
                                    );
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1500);
                                })
                                .catch(error => {
                                    Swal.fire("Error",
                                        "Hubo un problema al eliminar la categoría.",
                                        "error");
                                });
                        }
                    });
                });
            });
        });
    </script>

    <script>
        @if ($errors->any())
            let errorMessages = "";
            @foreach ($errors->all() as $error)
                errorMessages += "{{ $error }}\n";
            @endforeach

            Swal.fire({
                title: "Errores de Validación",
                text: errorMessages,
                icon: "warning",
                confirmButtonText: "Revisar"
            });
        @endif
    </script>
@stop
