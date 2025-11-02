@extends('adminlte::page')

@section('title', 'AdminJugueria')

@section('content_header')
    <h1>Listado de Usuarios</h1>
@stop

@section('content')
    @livewire('admin.user-index')
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

        .view-btn {
            background-color: #17a2b8;
        }

        .view-btn:hover {
            background-color: #138496;
        }

        .floating-btn-form {
            display: inline-block;
        }
    </style>
@stop

@section('js')
    <script>
        console.log("Hi, I'm using the Laravel-AdminLTE package!");
    </script>
@stop
