@extends('adminlte::page')
@section('title', 'Detalle de Categoría')
@section('content_header')
    <h1>Detalle de la Categoría</h1>
@stop
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="container-fluid">
                <div class="form-container">
                    <h2 class="text-center mb-4">Información de la Categoría</h2>
                    <div class="info-box">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>ID:</th>
                                    <td>{{ $categorium->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nombre:</th>
                                    <td>{{ $categorium->nombre_categoria }}</td>
                                </tr>
                                <tr>
                                    <th>Descripción:</th>
                                    <td>{{ $categorium->descripcion }}</td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        <span
                                            class="badge {{ $categorium->status == 1 ? 'badge-success' : 'badge-danger' }}">
                                            {{ $categorium->status == 1 ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Imagen:</th>
                                    <td class="text-center">
                                        @if ($categorium->image->count())
                                            <img src="{{ Storage::url($categorium->image->first()->url) }}"
                                                class="image-preview">
                                        @else
                                            <p><small>Sin imagen</small></p>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between mt-3" style="width: 100%">
                        <a href="{{ route('admin.categoria.edit', $categorium) }}" style="width: 50%"
                            class="btn btn-primary btn-custom">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('admin.categoria.index') }}" style="width: 50%" class="btn">
                            <i class="fas fa-times"></i>🔙 Cancelar</a>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="floating-btn-container">
            <!-- 🔹 Botón para Agregar Subtítulo -->
            <a href="{{ route('admin.categoria.edit', $categorium) }}" class="floating-btn" title="Agregar Subtítulo">
                <i class="fas fa-edit"></i>
            </a>
            <!-- 🔙 Botón para Regresar -->
            <a href="{{ route('admin.categoria.index') }}" class="floating-btn back-btn" title="Regresar">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>
@stop

@section('css')
    <style>
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .info-box {
            width: 100%;
        }

        .table th {
            width: 30%;
            text-align: left;
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
            justify-content: space-between;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 15px;
            }

            .d-flex {
                flex-direction: column;
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
