<x-app-layout>
    <div>
        <div>
        </div>
        @if (session('success'))
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Éxito!",
                        text: "{{ session('success') }}",
                        icon: "success",
                        confirmButtonColor: "#135287",
                        confirmButtonText: "Aceptar"
                    });
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lo sentimos...',
                        text: '{{ session('error') }}',
                        confirmButtonColor: '#d33'
                    });
                });
            </script>
        @endif
        @if (session('alert'))
            <script>
                Swal.fire({
                    title: "¡Atención!",
                    text: "{{ session('alert') }}",
                    icon: "warning",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Entendido"
                });
            </script>
        @endif
    </div>
    <div class="mt-24">
        <div class="py-4">
            <nav class="flex justify-center items-center space-x-2 text-gray-600 text-sm sm:text-base">
                <a href="/" class="text-gray-500 hover:text-gray-800 font-medium">Home</a>
            </nav>
        </div>
        <div class="mb-10 pb-10">
            @php
                $empresa = App\Models\Empresa::get();
            @endphp

            <div class="relative w-full min-h-[60vh] flex items-center justify-center overflow-hidden">

                <!-- Video de fondo con overlay -->
                <div class="absolute inset-0 z-0 ">
                    <video class="w-full h-full object-cover" autoplay loop muted>
                        <source src="{{ asset('storage/Empresa/videojugo.mp4') }}" type="video/mp4">
                        Tu navegador no soporta la reproducción de videos.
                    </video>

                </div>

                <!-- Contenido sobre el video -->
                <div
                    class="relative z-10 bg-white bg-opacity-90 backdrop-blur-md shadow-2xl rounded-lg p-6 sm:p-10 w-[90%] max-w-xl text-center">
                    @foreach ($empresa as $item)
                        <p class="text-gray-500 text-sm tracking-wide">Bienvenidos</p>
                        <h1 class="text-4xl font-bold text-gray-900 leading-tight mt-2">{{ $item->nombre }}</h1>
                        <p class="text-gray-700 mt-4 text-lg">{{ $item->descripcion }}</p>
                        <a href="{{ route('views.nosotros') }}"
                            class="mt-6 inline-block bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-6 py-2 rounded-lg text-lg font-semibold shadow-md hover:scale-105 hover:shadow-xl transition-all">
                            Más información
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">

            @foreach ($paginas as $pagina)
                <div class="my-8">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $pagina->titulo_paginas }}</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 justify-center">
                    @forelse ($pagina->subtitulos as $subtitulo)
                        <a href="{{ route('views.parrafo', ['pagina' => $pagina->titulo_paginas, 'subtitulo' => $subtitulo->id]) }}"
                            class="block bg-white p-6 shadow-lg rounded-lg border border-gray-200 text-center transform hover:scale-105 transition duration-300">

                            <!-- 🟢 Imagen con fondo circular -->
                            <div
                                class="w-16 h-16 bg-blue-500 rounded-full overflow-hidden flex items-center justify-center mb-4">
                                <img src="{{ asset('storage/' . $subtitulo->image->url) }}"
                                    alt="{{ $subtitulo->titulo_subtitulo }}"
                                    class="w-full h-full object-cover rounded-full">
                            </div>


                            <h3 class="text-lg font-semibold text-gray-900">{{ $subtitulo->titulo_subtitulo }}</h3>
                            <p class="text-gray-600 mb-4 text-sm text-ellipsis overflow-hidden w-full"
                                style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                {{ $subtitulo->resumen }}
                            </p>
                        </a>
                    @empty
                        <p class="text-gray-500 text-center col-span-3">No hay subtítulos disponibles.</p>
                    @endforelse
                </div>
            @endforeach

        </div>





        <!-- Sección de Categorías -->
        <h1 class="text-3xl font-bold text-gray-900 text-center py-8">Categorías de Productos</h1>
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($categoria as $categorias)
                    <div
                        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 flex flex-col h-full">
                        <!-- Imagen de la categoría -->
                        @if ($categorias->image->isNotEmpty())
                            <img src="{{ asset('storage/' . $categorias->image->first()->url) }}"
                                alt="{{ $categorias->nombre_categoria }}"
                                class="w-full h-48 object-cover p-1 rounded-2xl">
                        @else
                            <div class="h-48 bg-gray-300 flex items-center justify-center text-gray-500">
                                <span>Imagen No Disponible</span>
                            </div>
                        @endif

                        <!-- Contenido de la Tarjeta -->
                        <div class="p-4 flex flex-col flex-grow">
                            <!-- Nombre de la categoría -->
                            <h2 class="text-lg font-semibold text-gray-800 mb-2">{{ $categorias->nombre_categoria }}
                            </h2>

                            <!-- Descripción (Texto truncado) -->
                            <p class="text-gray-600 mb-4 line-clamp-3">
                                {{ $categorias->descripcion }}
                            </p>

                            <!-- Botón al pie -->
                            <div class="mt-auto flex justify-center">
                                <a href="{{ route('views.productos') . '?categoria=' . $categorias->id }}"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 hover:scale-105 transition duration-300 shadow-md">
                                    Ver Productos
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


            <!-- Paginación -->
            <div class="mt-8">
                {{ $categoria->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
