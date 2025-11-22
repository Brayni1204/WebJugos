@extends('adminlte::page')

@section('title', 'AdminJugueria - Roles')

@section('content_header')
    <h1>Listado de Roles</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Crear Rol</a>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Rol</th>
                        <th colspan="2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>{{ $role->name }}</td>
                            <td width="10px">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-primary"><i
                                        class="fas fa-edit"></i></a>
                            </td>
                            <td width="10px">
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i
                                            class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="floating-btn-container">
        <a href="{{ route('admin.roles.create') }}" class="floating-btn" title="Crear Rol">
            <i class="fas fa-plus"></i>
        </a>
        <a href="{{ route('admin.users.index') }}" class="floating-btn back-btn" title="Regresar">
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
