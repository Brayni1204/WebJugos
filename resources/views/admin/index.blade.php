@extends('adminlte::page')
@section('title', 'admin Jugueria')
@section('content')
    <div class="flex flex-col items-center justify-center min-h-[90vh] bg-gradient-to-r   px-4">
        <div class="max-w-3xl w-full p-8 text-center">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-4">admin Juguería</h1>
            @php
                $menu = App\Models\Empresa::get();
                $ultimaImagen = $menu->isNotEmpty() ? $menu->first()->image_m()->latest()->first() : null;
            @endphp
            <div class="flex justify-center">
                <img class="h-24 md:w-96 md:h-32  object-cover"
                    src="{{ $ultimaImagen ? asset('storage/' . $ultimaImagen->url) : 'https://tailwindui.com/plus/img/logos/mark.svg?color=indigo&shade=500' }}"
                    alt="Logo">
            </div>
            <p class="mt-6 text-lg md:text-xl text-gray-700 leading-relaxed">
                ¡Bienvenido Administrador!

            </p>
            <div class="mt-6">
                <a href="{{ route('admin.nuevospedidosadmin.index') }}"
                    class="bg-blue-500 text-white text-lg font-semibold px-6 py-3 rounded-lg shadow-md transition duration-300">
                    Ir a Pedidos
                </a>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        console.log("Panel de Administración de la Juguería cargado correctamente.");
    </script>
@stop
