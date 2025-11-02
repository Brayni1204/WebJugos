<x-app-layout>
    <!-- 🏷️ Mini Navegación -->
    <div class="mb-5 mt-24">
        <div class="py-4">
            <div class="container mx-auto px-4">
                <nav class="flex items-center space-x-4 text-gray-600 justify-center">
                    <a href="/" class="text-gray-500 hover:text-gray-800 font-medium">Home</a>
                    <span>/</span>
                    <a href="" class="text-gray-800 font-semibold">{{ $pagina->titulo_paginas }}</a>
                </nav>
            </div>
        </div>

        <!-- 🏷️ Contenedor Principal -->
        <div class="pagina-container bg-gradient-to-r from-gray-100 via-white to-gray-100 min-h-screen pt-10 px-5">
            <div class="flex flex-col gap-10">

                <!-- 📌 Artículo Principal con Imagen de Fondo -->
                <article class="relative w-full h-[600px] bg-cover bg-center shadow-lg rounded-lg overflow-hidden"
                    style="background-image: url({{ Storage::url($pagina->image_pagina->url) }});">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-40 flex flex-col justify-center items-center text-center px-8">
                        <div class="bg-white bg-opacity-75 p-6 rounded-lg shadow-lg max-w-3xl">
                            <h1 class="text-4xl font-extrabold text-gray-800 mb-6 leading-tight">
                                {{ $pagina->titulo_paginas }}
                            </h1>

                            <p class="text-lg text-gray-600 text-center leading-relaxed hidden md:block">
                                {{ $pagina->resumen }}
                            </p>

                            <p class="text-lg text-gray-600 text-center leading-relaxed md:hidden">
                                {{ Str::limit($pagina->resumen, 100, '...') }}
                            </p>
                        </div>
                    </div>
                </article>

                <!-- 📌 Subtítulos -->
                <div class="container mx-auto px-4">
                    <h2 class="text-3xl font-semibold text-center text-gray-800 mb-8">
                        Más Contenido {{ $pagina->titulo_paginas }}
                    </h2>

                    @if ($pagina->Subtitulo->isNotEmpty())
                        @php
                            $totalItems = $pagina->Subtitulo->count();
                        @endphp

                        <div class="flex flex-wrap justify-center gap-8">
                            @foreach ($pagina->Subtitulo as $subtitulo)
                                <div
                                    class="w-full sm:w-[48%] md:w-[36%] lg:w-[30%] bg-white rounded-lg shadow-lg p-2 
                                    flex flex-col items-center text-center transition transform hover:scale-105 hover:shadow-2xl">
                                    <h3 class="text-2xl font-semibold text-gray-800 mb-3">
                                        {{ $subtitulo->titulo_subtitulo }}
                                    </h3>
                                    <p class="text-gray-600 mb-4 text-sm text-ellipsis overflow-hidden w-full"
                                        style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                        {{ $subtitulo->resumen }}
                                    </p>
                                    @if ($subtitulo->image)
                                        <img src="{{ asset('storage/' . $subtitulo->image->url) }}"
                                            alt="{{ $subtitulo->titulo_subtitulo }}"
                                            class="h-48 w-full object-cover rounded-lg shadow-md mb-4">
                                    @endif
                                    <div>
                                        <a href="{{ route('views.parrafo', ['pagina' => $pagina->titulo_paginas, 'subtitulo' => $subtitulo->id]) }}"
                                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300">
                                            Ver Información
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>

        <div class="carrusel-container overflow-hidden relative w-full">
            @if ($productos->isNotEmpty())
                <div class="container mx-auto mt-10">
                    <h2 class="text-3xl font-semibold text-center text-gray-800 mb-6">Lo que te ofrecemos</h2>

                    <div class="relative w-full">
                        <div id="carrusel" class="flex space-x-4 items-center w-max animate-scroll">
                            @foreach ($productos as $producto)
                                @if ($producto->image && $producto->image->isNotEmpty())
                                    <a href="{{ route('views.productos') . '?categoria=' . $producto->id_categoria }}">
                                        <img src="{{ asset('storage/' . $producto->image->first()->url) }}"
                                            alt="{{ $producto->nombre_producto }}"
                                            class="h-10 w-14 object-cover rounded-md shadow-md transition-transform transform hover:scale-110">
                                    </a>
                                @endif
                            @endforeach
                            <!-- 🔄 Duplicar imágenes para efecto infinito -->
                            @foreach ($productos as $producto)
                                @if ($producto->image && $producto->image->isNotEmpty())
                                    <a href="{{ route('views.productos') . '?categoria=' . $producto->id_categoria }}">
                                        <img src="{{ asset('storage/' . $producto->image->first()->url) }}"
                                            alt="{{ $producto->nombre_producto }}"
                                            class="h-10 w-14 object-cover rounded-md shadow-md transition-transform transform hover:scale-110">
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <style>
            .carrusel-container {
                display: flex;
                justify-content: center;
                align-items: center;
                height: auto;
                overflow: hidden;
                padding: 10px 0;
            }

            /* 🔹 Animación infinita fluida */
            @keyframes scroll {
                from {
                    transform: translateX(0);
                }

                to {
                    transform: translateX(-30%);
                }
            }

            .animate-scroll {
                display: flex;
                width: max-content;
                animation: scroll 15s linear infinite;
            }
        </style>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const carrusel = document.getElementById("carrusel");

                function moverCarrusel() {
                    const primerItem = carrusel.firstElementChild;
                    carrusel.appendChild(primerItem.cloneNode(true)); // Clona la imagen al final
                    carrusel.removeChild(primerItem); // Elimina la primera imagen
                }

                // 🔹 Iniciar carrusel automático
                setInterval(moverCarrusel, 2500);
            });
        </script>

    </div>
</x-app-layout>
