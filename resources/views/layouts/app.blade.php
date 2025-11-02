<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Obtener el favicon desde la base de datos -->
    @php
        $empresa = \App\Models\Empresa::latest()->first(); // Obtener el último registro
        $favicon =
            $empresa && $empresa->favicon_url
                ? asset('storage/' . $empresa->favicon_url)
                : asset('default-favicon.ico');
    @endphp
    <title>{{ $empresa->nombre ?? 'My Empresa' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ $favicon }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $favicon }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $favicon }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $favicon }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Styles -->
    @livewireStyles
</head>


<body class="font-sans antialiased flex flex-col min-h-screen">
    <!-- Banner -->
    <x-banner />

    <!-- Contenedor principal -->
    <div class="flex-grow bg-gray-100">
        @livewire('navegacion')
        @if (session('alert'))
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "{{ session('alert.type') }}", // success, error, warning, info, question
                        title: "{{ session('alert.title') }}",
                        text: "{{ session('alert.message') }}",
                        confirmButtonText: "Aceptar",
                        timer: 3000, // Se cierra automáticamente en 3 segundos
                        showCancelButton: false
                    });
                });
            </script>
        @endif

        <main>
            {{ $slot }}
        </main>
    </div>

    @livewire('footer')

    @stack('modals')

    @livewireScripts
</body>

</html>
