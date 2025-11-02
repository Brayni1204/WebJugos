@extends('adminlte::page')

@section('title', 'AdminJugueria')

@section('content_header')
    <h1>Mesas </h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.mesas.create') }}" class="btn btn-primary">Agregar Nueva Mesa</a>
        </div>
        <div class="card-body" style="overflow: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Estado</th>
                        <th>Código QR</th>
                        <th>Status</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mesas as $mesa)
                        <tr>
                            <td> Mesa N° - {{ $mesa->numero_mesa }}</td>
                            <td>{{ $mesa->estado }}</td>
                            <td>
                                @if ($mesa->codigo_qr)
                                    <img src="{{ $mesa->generarQr() }}" alt="QR Mesa {{ $mesa->numero_mesa }}" width="80">
                                @else
                                    <span>No disponible</span>
                                @endif
                            </td>
                            <td>
                                @if ($mesa->status == '1')
                                    <samp>Activa</samp>
                                @elseif ($mesa->status == '0')
                                    <span>
                                        Inhabilitada
                                    </span>
                                @endif
                            </td>
                            <td width="10px">
                                <div class="h-full" style="height: 80px">
                                    <div class="d-flex align-items-center gap-2 h-full" style="height: 100%; gap: 2px">
                                        <a href="{{ route('admin.mesas.show', $mesa->id) }}" class="btn btn-info btn-sm"
                                            title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <form action="{{ route('admin.mesas.toggle-status', $mesa->id) }}" method="POST"
                                            class="toggle-status-form" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-sm {{ $mesa->status == '1' ? 'btn-warning' : 'btn-success' }}"
                                                title="{{ $mesa->status == '1' ? 'Inhabilitar' : 'Habilitar' }}">
                                                <i class="fas {{ $mesa->status == '1' ? 'fa-lock' : 'fa-unlock' }}"></i>
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="floating-btn-container">
            <a href="{{ route('admin.mesas.create') }}" class="floating-btn" title="Agregar Mesa">
                <i class="fas fa-plus"></i>
            </a>
            <a href="{{ route('admin.home') }}" class="floating-btn back-btn" title="Regresar">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>
@stop

@section('css')
    <style>
        /* 🎨 Contenedor de Botones */
        .floating-btn-container {
            position: fixed;
            bottom: 2px;
            right: 2px;
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Captura el submit del formulario toggle-status para mostrar confirmación
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('.toggle-status-form');

            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // prevenir submit inmediato

                    const button = form.querySelector('button[type="submit"]');
                    const action = button.title; // "Habilitar" o "Inhabilitar"

                    Swal.fire({
                        title: `¿Seguro que quieres ${action.toLowerCase()} esta mesa?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: `Sí, ${action}`,
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // enviar formulario
                        }
                    });
                });
            });

            // Mostrar mensaje de éxito si existe en sesión (flash)
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: "{{ session('success') }}",
                    timer: 2500,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            @endif
        });
    </script>
@stop
