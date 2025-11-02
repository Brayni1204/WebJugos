@extends('adminlte::page')

@section('title', 'Empresa - Editar')

@section('content_header')
    <h1 class="text-center">Editar Empresa</h1>
@stop

@section('content')
    <div class="container">
        <div class="card shadow-lg">
            <div class="card-body">
                <form action="{{ route('admin.empresa.update', $empresa->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Empresa</label>
                        <input type="text" id="nombre" name="nombre" class="form-control"
                            value="{{ $empresa->nombre }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="mision" class="form-label">Misión</label>
                            <textarea id="mision" style="height: 180px;" name="mision" class="form-control" rows="3" required>{{ $empresa->mision }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="vision" class="form-label">Visión</label>
                            <textarea id="vision" style="height: 180px;" name="vision" class="form-control" rows="3" required>{{ $empresa->vision }}</textarea>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="mapa_url" class="form-label">Mapa URL</label>
                            <input type="text" id="mapa_url" name="mapa_url" class="form-control"
                                value="{{ $empresa->mapa_url }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="departamento" class="form-label">Departamento</label>
                            <input type="text" id="departamento" name="departamento" class="form-control"
                                value="{{ $empresa->departamento }}" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label for="provincia" class="form-label">Provincia</label>
                            <input type="text" id="provincia" name="provincia" class="form-control"
                                value="{{ old('provincia', $empresa->provincia) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="distrito" class="form-label">Distrito</label>
                            <input type="text" id="distrito" name="distrito" class="form-control"
                                value="{{ old('distrito', $empresa->distrito) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="calle" class="form-label">Calle</label>
                            <input type="text" id="calle" name="calle" class="form-control"
                                value="{{ old('calle', $empresa->calle) }}" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" class="form-control"
                                value="{{ $empresa->telefono }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="delivery" class="form-label">Costo de Delivery</label>
                            <input type="number" id="delivery" step="0.01" name="delivery" class="form-control"
                                value="{{ $empresa->delivery }}" required>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="favicon" class="form-label">Favicon</label>
                            <input type="file" name="favicon" id="favicon" class="form-control" accept="image/*"
                                onchange="previewImage(event, 'faviconPreview')">
                            <div class="text-center mt-2">
                                <img id="faviconPreview" src="{{ asset('storage/' . $empresa->favicon_url) }}"
                                    class="img-thumbnail" width="150">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="image" class="form-label">Imagen Relacionada</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*"
                                onchange="previewImage(event, 'imagePreview')">
                            <div class="text-center mt-2">
                                @if ($empresa->image_m)
                                    <img id="imagePreview" src="{{ asset('storage/' . $empresa->image_m->url) }}"
                                        class="img-thumbnail" width="150">
                                @else
                                    <img id="imagePreview" src="{{ asset('storage/default.png') }}"
                                        class="img-thumbnail" width="150">
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-4" style="width: 100%">

                        <button type="submit" style="width: 50%" class="btn btn-primary px-4 py-2">💾 Guardar
                            Cambios</button>
                        <a href="{{ route('admin.empresa.index') }}" style="width: 50%" class="btn px-4 py-2 ml-3">🔙
                            Cancelar</a>
                    </div>
                    <div class="floating-btn-container">
                        <button type="submit" title="Actualizar información" style="" class="btn btn-primary"><i
                                class="fas fa-edit"></i></button>
                        <a href="{{ route('admin.empresa.index') }}" class="floating-btn back-btn" title="Regresar">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card {
            border-radius: 10px;
        }

        .img-thumbnail {
            max-height: 150px;
            object-fit: cover;
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
        // Mostrar mensaje de éxito
        @if (session('success'))
            Swal.fire({
                title: "¡Éxito!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "OK"
            });
        @endif

        // Mostrar errores si hay algún fallo
        @if (session('error'))
            Swal.fire({
                title: "Error",
                text: "{{ session('error') }}",
                icon: "error",
                confirmButtonText: "OK"
            });
        @endif

        // Mostrar errores de validación
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
    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            event.preventDefault(); // Evita el envío automático

            Swal.fire({
                title: '¿Guardar cambios?',
                text: "Esta acción actualizará la información de la empresa.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, guardar'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit(); // Envía el formulario
                }
            });
        });

        function previewImage(event, id) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById(id);
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@stop
