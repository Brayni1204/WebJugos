<nav class="bg-white border-b border-gray-300 shadow-sm fixed top-0 left-0 right-0 w-full z-50 " x-data="{ open: false }">
    <div class="mx-auto max-w-7xl lg:px-8">
        <div class="relative flex items-center justify-between">

            <!-- Logo e ítems del menú -->
            @php
                $largeScreenLimit = 5;
                $mediumScreenLimit = 2;
            @endphp
            <div class="flex flex-1 items-center justify-between md:justify-between sm:justify-between"
                style="height: 90px">
                <!-- Botón de menú móvil -->
                <div class="inset-y-0 left-0 flex items-center sm:hidden">
                    <button type="button" x-on:click="open = !open"
                        class="inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-200 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gray-300"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"
                                x-show="!open" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"
                                x-show="open" />
                        </svg>
                    </button>
                </div>

                <div>
                    <a href="/" class="flex items-center">
                        @php
                            $empresa = \App\Models\Empresa::latest()->first();
                            $icono =
                                $empresa && $empresa->favicon_url
                                    ? asset('storage/' . $empresa->favicon_url)
                                    : asset('default-favicon.ico');
                        @endphp
                        <img class="h-16 sm:h16 w-auto" src="{{ $icono }}" alt="Logo">
                    </a>
                </div>

                <!-- Menú en pantallas grandes -->
                <div class="hidden sm:block">
                    <div class="w-full">
                        <div class="flex justify-center gap-10">
                            <a href="{{ route('views.productos') }}"
                                class="px-3 py-2 sm:px-1 sm:py-1 md:px-2 rounded-md text-gray-700 hover:bg-gray-200 hover:text-gray-900 text-xl font-bold">
                                Productos
                            </a>
                            <a href="{{ route('views.nosotros') }}"
                                class="px-3 py-2 sm:px-1 sm:py-1 md:px-2 rounded-md text-gray-700 hover:bg-gray-200 hover:text-gray-900 text-xl font-bold">
                                Nosotros
                            </a>
                            @foreach ($paginas as $index => $pagina)
                                @if ($index < $mediumScreenLimit)
                                    <a href="{{ route('views.pagina', $pagina->titulo_paginas) }}"
                                        class="px-3 py-2 sm:px-1 sm:py-1 md:px-2 rounded-md text-gray-700 hover:bg-gray-200 hover:text-gray-900 text-xl font-bold">
                                        {{ $pagina->titulo_paginas }}
                                    </a>
                                @endif
                            @endforeach
                            @if ($paginas->count() > $mediumScreenLimit)
                                <div class="relative" x-data="{ openDropdown: false }">
                                    <button x-on:click="openDropdown = !openDropdown"
                                        class="px-3 py-2 sm:px-1 sm:py-1 md:px-2 rounded-md text-gray-700 hover:bg-gray-200 hover:text-gray-900 text-xl font-bold">
                                        Más
                                    </button>
                                    <div x-show="openDropdown" x-on:click.away="openDropdown = false"
                                        class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                                        @foreach ($paginas->slice($mediumScreenLimit) as $paginaExtra)
                                            <a href="{{ route('views.pagina', $paginaExtra->titulo_paginas) }}"
                                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                                {{ $paginaExtra->titulo_paginas }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>



                <div class=" inset-y-0 flex items-center pr-2 sm:static sm:inset-auto sm:pr-0 lg:flex md:flex gap-6">
                    <!-- Icono de carrito de compras -->
                    <div class="relative">
                        <a href="{{ route('carrito.ver') }}" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
                                class="bi bi-cart4" viewBox="0 0 16 16">
                                <path
                                    d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5M3.14 5l.5 2H5V5zM6 5v2h2V5zm3 0v2h2V5zm3 0v2h1.36l.5-2zm1.11 3H12v2h.61zM11 8H9v2h2zM8 8H6v2h2zM5 8H3.89l.5 2H5zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0m9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0" />
                            </svg>

                            @php
                                $cantidadCarrito = LaraCart::count();
                            @endphp
                            @if ($cantidadCarrito > 0)
                                <span
                                    class="absolute top-0 right-0 transform translate-x-2 -translate-y-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                    {{ $cantidadCarrito }}
                                </span>
                            @endif
                        </a>
                    </div>
                    @auth
                        <div class="ml-3" x-data="{ open: false }">
                            <button class="md:mr-1" x-on:click="open = true" type="button"
                                class="bg-gray-200 rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
                                <img class="h-10 w-10 rounded-full" src="{{ auth()->user()->profile_photo_url }}"
                                    alt="">
                            </button>
                            <div x-show="open" x-on:click.away="if (!event.target.closest('.menu-usuario')) open = false"
                                class="menu-usuario absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                                <a href="{{ route('profile.show') }}"
                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Perfil</a>

                                <a href="{{ route('views.pedidos') }}"
                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Pedidos</a>

                                @can('admin.home')
                                    <a href="{{ route('admin.home') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Dashboard</a>
                                @endcan

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100"
                                        onclick="event.preventDefault(); this.closest('form').submit();">Cerrar Sesión</a>
                                </form>
                            </div>
                        </div>
                    @else
                        <div>
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                                    <svg class="w-8 h-8 text-gray-950" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5.121 17.804A9 9 0 0112 15a9 9 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false"
                                    class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50 py-2">
                                    <a href="{{ route('login') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Login</a>
                                    <a href="{{ route('register') }}"
                                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Register</a>
                                </div>
                            </div>
                        </div>
                    @endauth


                </div>

            </div>

        </div>
    </div>

    <div x-show="open" x-on:click="open = false" class="fixed inset-0 bg-black bg-opacity-50 z-40" x-cloak></div>
    <div x-show="open" x-transition x-cloak
        class="fixed inset-y-0 left-0 w-1/2 bg-white shadow-lg z-50 overflow-y-auto">
        <!-- Título del menú -->
        <div class="px-4 py-3 border-b border-gray-300 text-center font-bold text-lg">
            Menú Lateral
        </div>
        <!-- Contenido del menú -->
        <div class="space-y-1 px-4 py-2">
            <a href="/"
                class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-200 hover:text-gray-900 text-xl font-bold">
                Home
            </a>
            <a href="{{ route('views.productos') }}"
                class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-200 hover:text-gray-900 text-xl font-bold">
                Productos
            </a>
            <a href="{{ route('views.nosotros') }}"
                class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-200 hover:text-gray-900 text-xl font-bold">
                Nosotros
            </a>
            @foreach ($paginas as $pagina)
                <a href="{{ route('views.pagina', $pagina->titulo_paginas) }}"
                    class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-200 hover:text-gray-900 text-xl font-bold">
                    {{ $pagina->titulo_paginas }}
                </a>
            @endforeach
        </div>
    </div>
</nav>
