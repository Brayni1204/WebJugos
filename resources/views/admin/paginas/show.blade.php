@extends('adminlte::page')

@section('title', 'Vista Previa de la Página')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="title-header">
            <i class="fas fa-eye"></i> Previsualizando:
            <span class="highlight">{{ $pagina->titulo_paginas }}</span>
        </h1>
        <span class="badge {{ $pagina->status == 2 ? 'badge-success' : 'badge-warning' }}">
            {{ $pagina->status == 2 ? 'Publicada' : 'Borrador' }}
        </span>
    </div>
@stop
@section('content')
    <div class="card">
        <div class="card-body text-center">
            <div class="flex flex-col gap-10">
                <div style="height: 300px;">
                    <article class="bg-cover bg-center w-full h-full"
                        style="background-image: url({{ $pagina->image_pagina->url }}); background-size: cover; background-position: center; height: 100%; display: flex; flex-direction: column; justify-content:center;">
                        <div class="text-center bg-white bg-opacity-75 p-6 rounded-lg shadow-lg">
                            <h2>
                                {{ $pagina->titulo_paginas }}
                            </h2>
                            <p class="text-sm text-gray-600">
                                {{ Str::limit($pagina->resumen, 450, '...') }}
                            </p>
                        </div>
                    </article>
                </div>
            </div>
            <div class="d-flex">
                <div class="d-flex">
                    <div class="d-flex" style="gap: 4px; padding: 10px; justify-content: center">
                        @foreach ($pagina->Subtitulo as $subtitulo)
                            <div class="border p-3 mb-3" style="width: 50%">
                                <h5>{{ $subtitulo->titulo_subtitulo }}</h5>
                                <p class="text-sm">{{ $subtitulo->resumen }}</p>
                                @if ($subtitulo->image)
                                    <img src="{{ $subtitulo->image->url }}" width="200">
                                @endif
                                <div class="mt-2">
                                    <a href="{{ route('admin.subtitulos.edit', $subtitulo) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form action="{{ route('admin.subtitulos.destroy', $subtitulo) }}" method="POST"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Eliminar subtítulo?')">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="floating-btn-container">
            <a href="{{ route('admin.subtitulos.create', ['id_pagina' => $pagina->id]) }}" class="floating-btn"
                title="Agregar Subtítulo">
                <i class="fas fa-plus"></i>
            </a>
            <a href=" {{ route('admin.paginas.edit', $pagina) }}" style="background-color: #007bff" class="floating-btn"
                title="Editar Página">
                <i class="fas fa-edit"></i>
            </a>
            <!-- 👁️ Botón para Ver Subtítulo (Si existen subtítulos) -->
            @if ($pagina->Subtitulo->count() > 0)
                <a href="{{ route('admin.subtitulos.show', $subtitulo) }}" class="floating-btn view-btn"
                    title="Ver Subtítulo">
                    <i class="fas fa-eye"></i>
                </a>
            @endif
            <!-- 📝 Formulario para Cambiar Estado de la Página -->
            <form action="{{ route('admin.paginas.update', $pagina) }}" method="POST" class="floating-btn-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="{{ $pagina->status == 2 ? 1 : 2 }}">
                <button type="submit"
                    class="floating-btn status-btn {{ $pagina->status == 2 ? 'draft-btn' : 'publish-btn' }}"
                    title="{{ $pagina->status == 2 ? 'Cambiar a Borrador' : 'Publicar Página' }}">
                    <i class="fas {{ $pagina->status == 2 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
                </button>
            </form>
            <a href="{{ route('admin.paginas.index') }}" class="floating-btn back-btn" title="Regresar">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>
@stop

@section('css')
    <style>
        .floating-btn-container {
            position: fixed;
            bottom: 5px;
            right: 5px;
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
