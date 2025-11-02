@extends('adminlte::page')

@section('title', 'Vista Subtítulos')

@section('content_header')
    <div style="display: flex; justify-content: center;">
        <h1 class="text-2xl font-semibold">Vista de Subtítulos y Párrafos</h1>
    </div>
@stop

@section('content')
    <div class="mb-10 pb-10">

        <div style="display: flex; justify-content: space-between; width: 100% ">
            <div style="width: 80%">
                <div style="width: 100%">
                    <div style="width: 100%; display: flex; justify-content: center">
                        @if ($subtitulo->image)
                            <img src="{{ Storage::url($subtitulo->image->url) }}" alt="{{ $subtitulo->titulo_subtitulo }}"
                                class="object-cover rounded-lg mb-6" style="width: 80%">
                        @endif
                    </div>
                </div>
                <div style=" display: flex; justify-content: center">
                    <div style="width: 80%">
                        <h2 class="text-3xl font-semibold mb-4">{{ $subtitulo->titulo_subtitulo }}</h2>
                        <p>
                            {{ $subtitulo->resumen }}
                        </p>
                    </div>
                </div>
                <div style=" display: flex; justify-content: center">
                    <div style="width: 80%">
                        <ul class="space-y-4">
                            @foreach ($subtitulo->Parrafo as $key => $parrafo)
                                <li class="bg-gray-100 p-4 rounded-lg flex items-center space-x-4">
                                    @if (($key + 1) % 2 != 0)
                                    @endif
                                    <div>
                                        {{ $parrafo->contenido }}
                                        <a href="{{ route('admin.parrafos.edit', $parrafo->id) }}">
                                            editar
                                        </a>
                                    </div>
                                    @if (($key + 1) % 2 == 0)
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>
            <div style="width: 20%">
                <h3 class="text-xl font-medium mb-4 border-b border-gray-700 pb-2">Más subtítulos</h3>
                @if ($subtitulosRelacionados->isNotEmpty())
                    <ul class="space-y-4">
                        @foreach ($subtitulosRelacionados as $relacionado)
                            <li class="flex items-center space-x-4">
                                <div>
                                    <a href="{{ route('admin.subtitulos.show', $relacionado->id) }}"
                                        class="text-sm font-medium text-gray-800 hover:underline">
                                        {{ $relacionado->titulo_subtitulo }}
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500 italic">No hay subtítulos relacionados.</p>
                @endif
            </div>
        </div>


        <div class="floating-btn-container">
            <a href=" {{ route('admin.subtitulos.edit', $subtitulo) }}" style="background-color: #007bff"
                class="floating-btn" title="Editar Subtitulo">
                <i class="fas fa-edit"></i>
            </a>
            <!-- 🔹 Botón para Agregar Párrafo -->
            <a href="{{ route('admin.parrafos.create', ['id_subtitulo' => $subtitulo->id]) }}" class="floating-btn"
                title="Agregar Párrafo">
                <i class="fas fa-plus"></i>
            </a>

            <!-- 🔙 Botón para Regresar -->
            <a href="{{ route('admin.paginas.show', ['pagina' => $subtitulo->id_pagina]) }}" class="floating-btn back-btn"
                title="Regresar">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>

    </div>
@stop

@section('css')
    <style>
        .floating-btn-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .floating-btn {
            background-color: #007bff;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            transition: background 0.3s, transform 0.3s;
            text-decoration: none;
        }

        .floating-btn:hover {
            background-color: #0056b3;
            transform: scale(1.1);
        }

        /* 🎨 Diferente color para el botón de regresar */
        .back-btn {
            background-color: #dc3545;
        }

        .back-btn:hover {
            background-color: #b02a37;
        }

        .floating-btn i {
            color: white;
        }
    </style>
@stop
