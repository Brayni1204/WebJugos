@extends('adminlte::page')

@section('title', 'Editar Párrafo')

@section('content_header')
    <h1>Editar Párrafo de: <span class="text-blue-600">{{ $subtitulo->titulo_subtitulo }}</span></h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="container-fluid">
                <div class="form-container">
                    <h2 class="text-center mb-4">Modificar Párrafo</h2>

                    {{-- ⚠️ ALERTA DE ERRORES --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- FORMULARIO DE EDICIÓN --}}
                    <form id="parrafoForm" action="{{ route('admin.parrafos.update', $parrafo->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Subtítulo asociado (solo lectura) --}}
                        <div class="form-group">
                            <label>Subtítulo</label>
                            <input type="text" class="form-control" value="{{ $subtitulo->titulo_subtitulo }}" readonly>
                            <input type="hidden" name="id_subtitulo" value="{{ $subtitulo->id }}">
                        </div>

                        {{-- Contenido del párrafo --}}
                        <div class="form-group">
                            <label>Contenido</label>
                            <textarea name="contenido" class="form-control" style="height: 200px" required>{{ old('contenido', $parrafo->contenido) }}</textarea>
                        </div>

                        {{-- Estado --}}
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="status" class="form-control">
                                <option value="1" {{ $parrafo->status == 1 ? 'selected' : '' }}>Borrador</option>
                                <option value="2" {{ $parrafo->status == 2 ? 'selected' : '' }}>Publicado</option>
                            </select>
                        </div>

                        {{-- Imagen Actual --}}
                        <div style="display: flex; justify-content: center">
                            <img style="width: 180px; height: 150px;" src="{{ Storage::url($parrafo->image->url) }}"
                                alt="{{ $parrafo->id_subtitulo }}" class=" object-cover rounded-lg">
                        </div>



                        {{-- Subir Nueva Imagen --}}
                        <div class="form-group">
                            <label>Subir Nueva Imagen</label>
                            <div class="custom-file" id="imageUpload">
                                <span class="custom-file-label">Haz clic para seleccionar una imagen</span>
                                <input type="file" name="imagen" id="imagen" accept="image/*">
                            </div>
                            <div style="display: flex; justify-content: center">
                                <img id="preview" class="image-preview">
                            </div>
                        </div>

                        {{-- BOTONES --}}
                        <div class="d-flex" style="gap: 10px">
                            <button type="submit" class="btn btn-primary btn-custom"><i class="fas fa-save"></i>
                                Actualizar</button>
                            <a href="{{ route('admin.subtitulos.show', $subtitulo->id) }}"
                                class="btn btn-danger btn-custom">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                        <div class="floating-btn-container">
                            <button type="submit" title="Actualizar Parrafo" class="btn btn-primary btn-custom"><i
                                    class="fas fa-edit"></i></button>

                            <!-- 🔙 Botón para Regresar -->
                            <a href="{{ route('admin.subtitulos.show', $subtitulo) }}" class="floating-btn back-btn"
                                title="Regresar">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@stop


{{-- SCRIPTS --}}
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // 📷 PREVISUALIZAR NUEVA IMAGEN
        document.getElementById('imagen').addEventListener('change', function(event) {
            let file = event.target.files[0];
            let preview = document.getElementById('preview');
            let label = document.querySelector('.custom-file-label');

            if (file) {
                let reader = new FileReader();
                reader.onload = function() {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
                label.textContent = file.name;
            } else {
                preview.style.display = 'none';
                label.textContent = "Haz clic para seleccionar una imagen";
            }
        });

        // 📌 MEJOR INTERACCIÓN PARA EL INPUT FILE
        document.getElementById('imageUpload').addEventListener('click', function() {
            document.getElementById('imagen').click();
        });

        // 📌 CONFIRMACIÓN CON SWEETALERT2
        document.getElementById('parrafoForm').addEventListener('submit', function(event) {
            event.preventDefault();

            Swal.fire({
                title: "¿Actualizar Párrafo?",
                text: "Se guardarán los cambios en este párrafo.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, actualizar"
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit();
                }
            });
        });
    </script>
@stop

{{-- CSS --}}
@section('css')
    <style>
        .form-container {
            max-width: 700px;
            margin: auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-group label {
            font-weight: bold;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .custom-file {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 10px;
            border: 2px dashed #ccc;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .custom-file:hover {
            background: #eef3f8;
            border-color: #007bff;
        }

        .custom-file input {
            display: none;
        }

        .custom-file-label {
            font-size: 16px;
            font-weight: 500;
            color: #555;
        }

        .image-preview {
            display: none;
            width: 100%;
            max-width: 250px;
            border-radius: 10px;
            margin-top: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-custom {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
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
