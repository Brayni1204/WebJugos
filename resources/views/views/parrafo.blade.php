<x-app-layout>
    <div class="mb-10 pb-10">
        <!-- 📌 Breadcrumbs -->
        <div class="py-10">
            <div class="container mx-auto px-4">
                <nav class="flex items-center justify-center space-x-4 text-gray-600">
                    <a href="/" class="text-gray-500 hover:text-gray-800 font-medium">Home</a>
                    <span>/</span>
                    @if (!empty($pagina))
                        <a href="{{ route('views.pagina', $pagina) }}" class="text-gray-600 font-semibold">
                            {{ $pagina }}
                        </a>
                    @endif
                    <span>/</span>
                    <a href="" class="text-gray-800 font-semibold">
                        {{ Str::words($subtitulo->titulo_subtitulo, 1, '') }}
                    </a>
                </nav>
            </div>
        </div>

        <!-- 📌 Contenido Principal -->
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- 📌 Contenido Central (Subtítulo y Párrafos) -->
                <div class="lg:col-span-4">
                    <!-- 🏷️ Título -->
                    <h2 class="text-3xl font-semibold text-center mb-6">
                        {{ $subtitulo->titulo_subtitulo }}
                    </h2>

                    <!-- 📷 Imagen -->
                    @if ($subtitulo->image)
                        <div class="flex justify-center mb-6">
                            <img src="{{ Storage::url($subtitulo->image->url) }}"
                                alt="{{ $subtitulo->titulo_subtitulo }}"
                                class="w-full max-w-4xl object-cover rounded-lg shadow-lg">
                        </div>
                    @endif

                    <!-- 📖 Resumen -->
                    <div class="bg-gray-100 p-6 rounded-lg shadow-sm mb-6">
                        <h3 class="text-lg font-semibold">Resumen de {{ $subtitulo->titulo_subtitulo }}</h3>
                        <p class="text-gray-700">{{ $subtitulo->resumen }}</p>
                    </div>

                    <!-- 📜 Párrafos -->
                    <ul class="space-y-4 list-disc list-inside">
                        @foreach ($subtitulo->Parrafo as $key => $parrafo)
                            <li class="bg-gray-100 p-4 rounded-lg">
                                {{ $parrafo->contenido }}
                            </li>
                        @endforeach
                    </ul>

                </div>

                <!-- 📌 Barra Lateral (Subtítulos Relacionados) -->
                <div class="lg:col-span-1">
                    <h3 class="text-xl font-medium mb-4 border-b border-gray-300 pb-2">Más Contenido</h3>
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                        @if ($subtitulosRelacionados->isNotEmpty())
                            <ul class="space-y-4">
                                @foreach ($subtitulosRelacionados as $relacionado)
                                    <li class="flex items-center space-x-4">
                                        <!-- Imagen en Miniatura -->
                                        @if ($relacionado->image)
                                            <img src="{{ Storage::url($relacionado->image->url) }}"
                                                alt="{{ $relacionado->titulo_subtitulo }}"
                                                class="w-16 h-16 object-cover rounded-lg shadow">
                                        @endif
                                        <!-- Título -->
                                        <div>
                                            <a href="{{ route('views.parrafo', ['pagina' => $pagina, 'subtitulo' => $relacionado->id]) }}"
                                                class="text-sm font-medium text-gray-800 hover:underline">
                                                {{ Str::words($relacionado->titulo_subtitulo, 3, '...') }}
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
