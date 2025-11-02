<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-24 mb-10 pb-10">

        <!-- 🟢 Breadcrumb (Navegación) -->
        <div class="py-4">
            <nav class="flex justify-center items-center space-x-2 text-gray-600 text-sm sm:text-base">
                <a href="/" class="text-gray-500 hover:text-gray-800 font-medium">Home</a>
                <span>/</span>
                <a href="" class="text-gray-800 font-semibold">Nosotros</a>
            </nav>
        </div>

        @if ($empresa->isNotEmpty())
            @php $info = $empresa->first(); @endphp

            <!-- 🟡 Logo y Nombre -->
            <div class="text-center mb-8">
                @if ($info->favicon_url)
                    <img src="{{ asset('storage/' . $info->favicon_url) }}" alt="Logo de {{ $info->nombre }}"
                        class="h-24 mx-auto mb-3">
                @endif
                <h1 class="text-3xl font-bold text-gray-900">{{ $info->nombre }}</h1>
                @if ($info->descripcion)
                    <p class="text-gray-600 mt-2 max-w-3xl mx-auto">{{ $info->descripcion }}</p>
                @endif
            </div>

            <!-- 🔵 Información General -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- 🏆 Misión -->
                <div
                    class="flex flex-col items-center text-center bg-white p-8 shadow-lg rounded-2xl border border-gray-200 transform hover:scale-105 transition duration-300">
                    <div class="bg-blue-500 p-4 rounded-full mb-4">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">Nuestra Misión</h2>
                    <p class="text-gray-700 mt-2">{{ $info->mision }}</p>
                </div>

                <!-- 🚀 Visión -->
                <div
                    class="flex flex-col items-center text-center bg-white p-8 shadow-lg rounded-2xl border border-gray-200 transform hover:scale-105 transition duration-300">
                    <div class="bg-green-500 p-4 rounded-full mb-4">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-900">Nuestra Visión</h2>
                    <p class="text-gray-700 mt-2">{{ $info->vision }}</p>
                </div>

                <!-- 🚚 Delivery -->
                <a href="{{ route('views.productos') }}"
                    class="flex flex-col items-center text-center bg-white p-8 shadow-lg rounded-2xl border border-gray-200 transform hover:scale-105 transition duration-300">
                    <div class="bg-yellow-500 p-4 rounded-full mb-4">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 13l4-4a1 1 0 011.414 0L14 15m4-4h3m-3 0a2 2 0 11-4 0m4 0l-4 4m-6 2H3m3 0a2 2 0 100-4m12 4a2 2 0 100-4">
                            </path>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Costo de Envío local</h2>
                    <p class="text-2xl font-bold text-green-600 mt-2">S/. {{ number_format($info->delivery, 2) }}
                    </p>
                </a>
            </div>

            <!-- 📍 Ubicación y Mapa -->
            <div class="bg-white p-6 rounded-lg shadow-md mt-6">
                <h2 class="text-2xl font-bold text-gray-900 text-center mb-4">Nuestra Ubicación</h2>
                <div class="grid md:grid-cols-2 gap-6">

                    <!-- 📍 Información de la Ubicación -->
                    <div>
                        <p class="text-gray-700">
                            <strong>📍 Departamento:</strong> {{ $info->departamento }}<br>
                            <strong>🏛 Provincia:</strong> {{ $info->provincia }}<br>
                            <strong>📌 Distrito:</strong> {{ $info->distrito }}<br>
                            <strong>🚏 Calle:</strong> {{ $info->calle }}
                        </p>
                    </div>

                    @if (!empty($info->mapa_url) && str_contains($info->mapa_url, '<iframe'))
                        <div class="w-full overflow-hidden rounded-lg">
                            <div class="relative" style="padding-top: 56.25%;">
                                {!! str_replace('<iframe', '<iframe class="absolute top-0 left-0 w-full h-full rounded-lg"', $info->mapa_url) !!}
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-center">No hay mapa disponible.</p>
                    @endif
                </div>
            </div>
        @else
            <p class="text-center text-gray-500">No hay información disponible de la empresa.</p>
        @endif

    </div>
</x-app-layout>
