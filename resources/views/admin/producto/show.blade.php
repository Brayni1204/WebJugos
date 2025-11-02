@extends('adminlte::page')

@section('title', 'Detalles del Producto')

@section('content_header')
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="container-fluid" style="padding: 0px;">
                <div class="form-container">
                    <h2 class="text-center mb-4">Información del Producto</h2>

                    <div class="info-box">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>ID:</th>
                                    <td>{{ $producto->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nombre:</th>
                                    <td>{{ $producto->nombre_producto }}</td>
                                </tr>
                                <tr>
                                    <th>Categoría:</th>
                                    <td>{{ $producto->categoria->nombre_categoria ?? 'No asignada' }}</td>
                                </tr>
                                <tr>
                                    <th>Descripción:</th>
                                    <td>{{ $producto->descripcion ?? 'Sin descripción' }}</td>
                                </tr>
                                <tr>
                                    <th>Stock:</th>
                                    <td>{{ $producto->stock }}</td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        <span class="badge {{ $producto->status == 1 ? 'badge-success' : 'badge-danger' }}">
                                            {{ $producto->status == 1 ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Precio de Venta:</th>
                                    <td>S/.
                                        {{ $producto->precios ? number_format($producto->precios->precio_venta, 2) : 'No asignado' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Precio de Compra:</th>
                                    <td>S/.
                                        {{ $producto->precios ? number_format($producto->precios->precio_compra, 2) : 'No asignado' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Imagen:</th>
                                    <td class="text-center">
                                        @if ($producto->image()->exists())
                                            <img src="{{ Storage::url($producto->image->first()->url) }}"
                                                class="image-preview">
                                        @else
                                            <p><small>Sin imagen</small></p>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex mt-3" style="width: 100%">
                        <div style="width: 50%">
                            <a href="{{ route('admin.producto.edit', $producto) }}" style="width: 100%"
                                class="btn btn-primary btn-custom">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </div>
                        <div style="width: 50%">
                            <a href="{{ route('admin.producto.index') }}" style="width: 100%" class="btn btn-custom">
                                <i class="fas fa-times"></i>🔙 Cancelar
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="floating-btn-container">
            <!-- 🔹 Botón para Agregar Subtítulo -->
            <a href="{{ route('admin.producto.edit', $producto) }}" class="floating-btn" title="Editar producto">
                <i class="fas fa-edit"></i>
            </a>

            <!-- 🔙 Botón para Regresar -->
            <a href="{{ route('admin.producto.index') }}" class="floating-btn back-btn" title="Regresar">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>
@stop

@section('css')
    <style>
        .form-container {
            max-width: 700px;
            margin: auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .info-box {
            padding: 0px;
            width: 100%;
        }

        .table th {
            width: 35%;
            text-align: left;
            background: #f8f9fa;
        }

        .badge {
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .image-preview {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-custom {
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
        }

        .d-flex {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 15px;
            }

            .d-flex {
                flex-direction: column;
                align-items: center;
            }

            .btn-custom {
                width: 100%;
            }
        }
    </style>
    <style>
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

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
