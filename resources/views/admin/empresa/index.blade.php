@extends('adminlte::page')

@section('title', 'Empresa')

@section('content_header')
@stop

@section('content')
    <div style="padding-bottom: 50px">
        @foreach ($empresa as $empresas)
            <!-- Sección de Nombre -->
            <div class="card shadow-lg mb-4">
                <div class="card-body">
                    <h2 class="text-center text-xl font-bold text-gray-900">Nombre de la Empresa</h2>
                    <p class="text-center text-lg text-gray-700">{{ $empresas->nombre ?? 'No existe' }}</p>
                </div>
            </div>

            <div class="card bg-white shadow-lg mb-4">
                <div class="w-full">
                    <div
                        class="flex flex-col md:flex-row items-center bg-white p-6 w-full min-h-[70vh] shadow-lg rounded-lg gap-6">
                        <!-- Sección de Texto -->
                        <div class="w-full md:w-1/2 p-6 flex flex-col justify-center text-center md:text-left">
                            <p class="text-gray-500 text-sm">Bienvenidos a</p>
                            <h1 class="text-4xl font-bold text-gray-900 leading-tight mt-2">
                                {{ $empresas->nombre }}
                            </h1>
                            <p class="text-gray-700 mt-4">{{ $empresas->descripcion }}</p>
                            <div class="mt-6">
                                <a href="{{ route('views.nosotros') }}"
                                    class="inline-block bg-cyan-500 text-white px-6 py-2 rounded-md text-lg font-semibold hover:bg-cyan-600 transition">
                                    Más información
                                </a>
                            </div>
                        </div>

                        <!-- Sección de Imagen -->
                        <div class="w-full md:w-1/2 flex justify-center">
                            <div
                                class="w-48 h-48 sm:w-64 sm:h-64 md:w-80 md:h-80 lg:w-96 lg:h-96 rounded-full border-8 border-yellow-400 overflow-hidden shadow-md">
                                @php
                                    $ultimaImagen = $empresa->first()->image_m()->latest()->first();
                                @endphp
                                @if ($ultimaImagen)
                                    <img class="w-full h-full object-cover"
                                        src="{{ asset('storage/' . $ultimaImagen->url) }}" alt="Robot">
                                @else
                                    <img class="w-full h-full object-cover" src="/mnt/data/image.png"
                                        alt="Robot por defecto">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Sección de Misión y Visión -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-lg">
                        <div class="card-body">
                            <h3 class="text-center text-lg font-bold text-gray-900">Misión</h3>
                            <p class="text-center text-gray-700">{{ $empresas->mision }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-lg">
                        <div class="card-body">
                            <h3 class="text-center text-lg font-bold text-gray-900">Visión</h3>
                            <p class="text-center text-gray-700">{{ $empresas->vision }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @php $info = $empresa->first(); @endphp
            <!-- Sección de Ubicación -->
            <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                <h2 class="text-2xl font-bold text-gray-900 text-center mb-4">Nuestra Ubicación</h2>
                <div class="grid md:grid-cols-2 gap-6">

                    <!-- 📍 Información de la Ubicación -->
                    <div>
                        <p class="text-gray-700">
                            <strong>📍 Departamento:</strong> {{ $info->departamento }}<br>
                            <strong>🏛 Provincia:</strong> {{ $info->provincia }}<br>
                            <strong>📌 Distrito:</strong> {{ $info->distrito }}<br>
                            <strong>🚏 Calle:</strong> {{ $info->calle }}
                        </p>
                    </div>

                    <!-- 🗺️ Mapa con Coordenadas -->
                    @if (!empty($info->mapa_url) && str_contains($info->mapa_url, '<iframe'))
                        <div class="w-full">
                            {!! $info->mapa_url !!}
                        </div>
                    @else
                        <p class="text-gray-500 text-center">No hay mapa disponible.</p>
                    @endif

                </div>
            </div>

            <!-- Sección de Información Adicional -->
            <div class="card shadow-lg mt-4">
                <div class="card-body">
                    <h3 class="text-center text-lg font-bold text-gray-900">Información Adicional</h3>
                    <p><strong>Teléfono:</strong> {{ $empresas->telefono }}</p>
                    <p><strong>Delivery:</strong> {{ $empresas->delivery }}</p>
                </div>
            </div>

            <!-- Sección de Imágenes -->
            <div style="display: flex; justify-content: center; width: 100%">
                <div>
                    <div class="mt-4">
                        <div>
                            <div class="card shadow-lg">
                                <div class="card-body text-center">
                                    <h4 class="font-bold text-gray-900">ICONO DEL NAVEGADOR</h4>
                                    <img src="{{ asset('storage/' . $empresas->favicon_url) }}" class="img-fluid rounded"
                                        alt="Favicon">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón de Editar -->
            <div class="d-flex justify-content-center mt-4">
                <a href="{{ route('admin.empresa.edit', $empresas->id) }}" class="btn btn-primary px-4 py-2">✏️ Editar
                    Información</a>
            </div>
        @endforeach
        <div class="floating-btn-container">
            <!-- 🔹 Botón para Agregar Subtítulo -->
            <a href="{{ route('admin.empresa.edit', $empresas->id) }}" class="floating-btn" title="Actualizar Informacion">
                <i class="fas fa-edit"></i>
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
        .card {
            border-radius: 10px;
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
    <script src="https://cdn.tailwindcss.com"></script>
@stop
