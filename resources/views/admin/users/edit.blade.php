@extends('adminlte::page')
@section('title', 'AdminJugueria')
@section('content_header')
    <h1>Asignar un rol</h1>
@stop
@section('content')
    @if (session('info'))
        <div class="alert alert-success">
            <strong>{{ session('info') }}</strong>
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <p class="h5">Nombre</p>
            <p class="form-control">{{ $user->name }}</p>
            <h2 class="h5">Listado de roles</h2>
            <div>
                <div>
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @foreach ($roles as $role)
                            <div>
                                <label>
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="mr-1"
                                        {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                    {{ $role->name }}
                                </label>
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-evenly mt-4">
                            <button type="submit" class="btn btn-primary">Asignar rol</button>
                            <div>
                                <a href="{{ route('admin.users.index') }}" class="btn px-4 py-2 ml-3">🔙
                                    Cancelar</a>
                            </div>
                        </div>
                        <div class="floating-btn-container">
                            <button type="submit" class="btn btn-primary btn-custom"><i class="fas fa-save"></i></button>
                            <a href="{{ route('admin.users.index') }}" class="floating-btn back-btn" title="Regresar">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@stop

@section('css')
    <style>
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
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Mostrar mensaje de éxito si existe en la sesión
        @if (session('success'))
            Swal.fire({
                title: "¡Éxito!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonText: "OK"
            });
        @endif

        // Mostrar mensaje de error si existe en la sesión
        @if (session('error'))
            Swal.fire({
                title: "Error",
                text: "{{ session('error') }}",
                icon: "error",
                confirmButtonText: "OK"
            });
        @endif

        // Confirmación antes de enviar el formulario de asignación de roles
        document.querySelector('form').addEventListener('submit', function(event) {
            event.preventDefault(); // Evita el envío automático

            Swal.fire({
                title: "¿Asignar estos roles?",
                text: "Se actualizarán los permisos del usuario.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, asignar"
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.submit(); // Envía el formulario si el usuario confirma
                }
            });
        });
    </script>

@stop

@stop
