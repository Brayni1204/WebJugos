@extends('adminlte::page')

@section('title', 'Nuevo Producto')

@section('content_header')
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="container-fluid">
                <div class="form-container">
                    <h2 class="text-center mb-4">Registrar Producto</h2>

                    <form action="{{ route('admin.producto.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Nombre del Producto</label>
                            <input type="text" name="nombre_producto" autocomplete="off" class="form-control"
                                value="{{ old('nombre_producto') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Categoría</label>
                            <select name="id_categoria" class="form-control">
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">{{ $categoria->nombre_categoria }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea name="descripcion" class="form-control">{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Stock</label>
                            <input type="number" name="stock" class="form-control" value="{{ old('stock', 0) }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Estado</label>
                            <select name="status" class="form-control">
                                <option value="1">Activo</option>
                                <option value="2">Inactivo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Precio de Venta</label>
                            <input type="number" name="precio_venta" class="form-control"
                                value="{{ old('precio_venta', 0) }}" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <input type="checkbox" id="togglePrecioCompra">
                            <label for="togglePrecioCompra">Agregar Precio de Compra</label>
                        </div>

                        <div class="form-group" id="precioCompraGroup" style="display: none;">
                            <label>Precio de Compra</label>
                            <input type="number" name="precio_compra" class="form-control"
                                value="{{ old('precio_compra', 0) }}" step="0.01">
                        </div>

                        <!-- Campo de Imagen con estilo personalizado -->
                        <div class="form-group">
                            <label>Imagen</label>
                            <div class="custom-file" id="imageUpload">
                                <span class="custom-file-label">Haz clic para seleccionar una imagen</span>
                                <input type="file" name="imagen" id="imagen" accept="image/*">
                            </div>
                            <div style="display: flex; justify-content: center">
                                <img id="preview" class="image-preview">
                            </div>
                        </div>

                        <div class="d-flex" style="display: flex; gap: 10px">
                            <button type="submit" class="btn btn-primary btn-custom"><i class="fas fa-save"></i>
                                Guardar</button>
                            <a href="{{ route('admin.producto.index') }}" class="btn btn-custom"><i
                                    class="fas fa-times"></i>🔙
                                Cancelar</a>
                        </div>
                        <div class="floating-btn-container">
                            <!-- 🔹 Botón para Agregar Subtítulo -->
                            <button type="submit" title="Guardar Producto" class="btn btn-primary btn-custom"><i
                                    class="fas fa-save"></i></button>
                            <!-- 🔙 Botón para Regresar -->
                            <a href="{{ route('admin.producto.index') }}" class="floating-btn back-btn" title="Regresar">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
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

        document.getElementById('togglePrecioCompra').addEventListener('change', function() {
            let precioCompraGroup = document.getElementById('precioCompraGroup');
            precioCompraGroup.style.display = this.checked ? 'block' : 'none';
        });

        // Mejor experiencia de usuario en el input file
        document.getElementById('imageUpload').addEventListener('click', function() {
            document.getElementById('imagen').click();
        });
    </script>
@stop
@section('css')
    <style>
        /* Estilo general del formulario */
        .form-container {
            max-width: 800px;
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
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.5);
        }

        /* Diseño para el input file */
        .custom-file {
            position: sticky;
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

        /* Imagen de previsualización */
        .image-preview {
            display: none;
            width: 100%;
            max-width: 250px;
            border-radius: 10px;
            margin-top: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Botones */
        .btn-custom {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-container {
                padding: 15px;
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
