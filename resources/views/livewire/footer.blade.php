<footer class="bg-lime-950 text-white" style="padding-top: 1rem; padding-bottom: 1rem;">
    <div class="container mx-auto grid grid-cols-1 md:grid-cols-4 gap-8 px-6">
        <div>
            <a href="/" class="flex items-center">
                <!-- Imagen del menú -->
                @if ($empresa->isNotEmpty())
                    @php
                        $ultimaImagen = $empresa->first()->image_m()->latest()->first();
                    @endphp
                    @if ($ultimaImagen)
                        <img class="h-12 w-auto" src="{{ asset('storage/' . $ultimaImagen->url) }}" alt="Logo">
                    @else
                        <img class="h-8 w-auto"
                            src="https://tailwindui.com/plus/img/logos/mark.svg?color=indigo&shade=500"
                            alt="Default Logo">
                    @endif
                @else
                    <img class="h-8 w-auto" src="https://tailwindui.com/plus/img/logos/mark.svg?color=indigo&shade=500"
                        alt="Default Logo">
                @endif
            </a>
        </div>
        <div>
            <h3 class="text-lg font-semibold mb-1">Páginas</h3>
            <ul>
                @foreach ($paginas as $pagina)
                    <li class="mb-2">
                        <a href="{{ route('views.pagina', ['pagina' => $pagina->slug]) }}" class="hover:underline">
                            {{ $pagina->titulo_paginas }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div>
            <h3 class="text-lg font-semibold mb-1">Categorías</h3>
            <ul>
                @foreach ($categoria->take($mostrarTodas ? $categoria->count() : 3) as $cat)
                    <li class="mb-2">
                        <a href="{{ route('views.productos') . '?categoria=' . $cat->id }}" class="hover:underline">
                            {{ $cat->nombre_categoria }}
                        </a>
                    </li>
                @endforeach
            </ul>

            @if ($categoria->count() > 3)
                <button wire:click="toggleCategorias" class="mt-2 text-blue-500 hover:underline">
                    {{ $mostrarTodas ? 'Ver menos' : 'Ver más' }}
                </button>
            @endif
        </div>

        <div>
            <h3 class="text-lg font-semibold mb-1">Productos</h3>
            <ul>
                <li class="mb-2">
                    <a href="{{ route('views.productos') }}" class="hover:underline"> Todos
                    </a>
                </li>

            </ul>
        </div>

    </div>
    <div class="flex md:gap-10 justify-center p-2 space-x-8 text-4xl">
        @php
            $info = $empresa->first(); // Tomar la primera empresa registrada
        @endphp
        <a href="https://www.facebook.com/profile.php?id=61574863073968" class="social-icon text-blue-600"><i
                class="fab fa-facebook"></i></a>
        <a href="https://www.instagram.com/merakifruit3?igsh=MXRpMTdkOGQ0MTI5OQ==" class="social-icon text-pink-500"><i
                class="fab fa-instagram"></i></a>
        <a href="https://www.tiktok.com/@merakifruit3?is_from_webapp=1&sender_device=pc"
            class="social-icon text-black"><i class="fab fa-tiktok"></i></a>
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $info->telefono) }}"
            class="social-icon text-green-500"><i class="fab fa-whatsapp"></i></a>
    </div>



    <!-- Créditos -->
    @foreach ($empresa as $item)
        <div class="pt-2 text-center text-gray-400 text-sm">
            &copy; {{ date('Y') }} - Todos los derechos reservados <a href=""
                class="text-blue-400 hover:underline">{{ $item->nombre }}</a>
        </div>
    @endforeach

</footer>
