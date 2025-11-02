@extends('adminlte::page')
@section('title', 'AdminJugueria')
@section('content_header')
    <h1>Paginas</h1>
@stop
@section('content')
    @if (session('info'))
        <div class="alert alert-success">
            <strong>{{ session('info') }}</strong>
        </div>
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.paginas.create') }}" class="btn btn-primary">
                Agregar Nueva <i class="fas fa-plus"></i>
            </a>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead class="text-center">
                    <tr>
                        <th>Titulo</th>
                        <th>Resumen</th>
                        <th>Imagen</th>
                        <th>Estado</th>
                        <th colspan="3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($pagina as $paginas)
                        <tr>
                            <td>{{ $paginas->titulo_paginas }}</td>
                            <td>{{ Str::limit($paginas->resumen, 100, '...') }}</td>
                            <td>
                                @if ($paginas->image_pagina)
                                    <img src="{{ Storage::url($paginas->image_pagina->url) }}"
                                        class="img-fluid rounded shadow" width="100">
                                @else
                                    <small>Sin imagen</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $paginas->status == 2 ? 'badge-success' : 'badge-warning' }}">
                                    {{ $paginas->status == 2 ? 'Publicada' : 'Borrador' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('admin.paginas.show', $paginas) }}" class="btn btn-sm text-danger">
                                        <i class="fas fa-eye fa-lg"></i>
                                    </a>
                                    <a href="{{ route('admin.paginas.edit', $paginas) }}" class="btn btn-sm text-primary">
                                        <i class="fas fa-edit fa-lg"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm text-danger delete-page"
                                        data-url="{{ route('admin.paginas.destroy', $paginas) }}">
                                        <i class="fas fa-times fa-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="floating-btn-container">
            <a href="{{ route('admin.paginas.create') }}" class="floating-btn" title="Crear Nueva">
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
        /* Botones Flotantes */
        .floating-btn-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: center;
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

        /* Estilos Responsive */
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
            document.querySelectorAll('.delete-page').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    let url = this.getAttribute("data-url");

                    Swal.fire({
                        title: "¿Eliminar esta página?",
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
                                        "La página ha sido eliminada.", "success");
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1500);
                                })
                                .catch(error => {
                                    Swal.fire("Error",
                                        "Hubo un problema al eliminar la página.",
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
