@extends('adminlte::page')

@section('title', 'AdminJugueria - Crear Rol')

@section('content_header')
    <h1>Crear Nuevo Rol</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Nombre del Rol</label>
                    <input type="text" name="name" id="name" class="form-control"
                        placeholder="Ingrese el nombre del rol" required>
                </div>

                <h2 class="h5">Lista de Permisos</h2>
                <div class="row">
                    @foreach ($permissions as $resource => $permissionList)
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <strong>Permisos para {{ $resource }}</strong>
                                    <label class="mb-0">
                                        <input type="checkbox" class="select-all">
                                        Todos
                                    </label>
                                </div>
                                <div class="card-body">
                                    @foreach ($permissionList as $permission)
                                        <div>
                                            <label>
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="permission-checkbox">
                                                @php
                                                    $parts = explode('.', $permission->name);
                                                    $action = end($parts);
                                                @endphp
                                                {{ $action }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary mt-2">Crear Rol</button>
            </form>
        </div>
    </div>
    <div class="floating-btn-container">
        <a href="{{ route('admin.roles.index') }}" class="floating-btn back-btn" title="Regresar">
            <i class="fas fa-arrow-left"></i>
        </a>
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

        .back-btn {
            background-color: #dc3545;
        }

        .back-btn:hover {
            background-color: #b02a37;
        }
    </style>
@stop

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckboxes = document.querySelectorAll('.select-all');

        selectAllCheckboxes.forEach(function (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function (event) {
                const card = event.target.closest('.card');
                const permissionCheckboxes = card.querySelectorAll('.permission-checkbox');

                permissionCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = event.target.checked;
                });
            });
        });
    });
</script>
@endsection
