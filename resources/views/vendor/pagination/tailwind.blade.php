@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        <div class="flex justify-between flex-1 sm:hidden">
            {{-- Botón Anterior --}}
            @if ($paginator->onFirstPage())
                <span
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-300 border border-gray-400 cursor-not-allowed leading-5 rounded-md">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-500 border border-blue-600 rounded-md hover:bg-blue-600 focus:outline-none focus:ring focus:ring-blue-300 transition duration-150">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            {{-- Botón Siguiente --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-white bg-blue-500 border border-blue-600 rounded-md hover:bg-blue-600 focus:outline-none focus:ring focus:ring-blue-300 transition duration-150">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <span
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-400 bg-gray-300 border border-gray-400 cursor-not-allowed leading-5 rounded-md">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700 dark:text-gray-400">
                    {!! __('Showing') !!}
                    @if ($paginator->firstItem())
                        <span class="font-medium">{{ $paginator->firstItem() }}</span> {!! __('to') !!}
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('of') !!}
                    <span class="font-medium">{{ $paginator->total() }}</span> {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rtl:flex-row-reverse shadow-sm rounded-md">
                    {{-- Botón Anterior --}}
                    @if ($paginator->onFirstPage())
                        <span
                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-gray-300 border border-gray-400 cursor-not-allowed rounded-l-md">
                            ❮
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}"
                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-white bg-blue-500 border border-blue-600 rounded-l-md hover:bg-blue-600 transition duration-150">
                            ❮
                        </a>
                    @endif

                    {{-- Números de Página --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default leading-5">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-700 cursor-default leading-5">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-white border border-gray-300 hover:bg-blue-100 transition duration-150">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Botón Siguiente --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}"
                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-white bg-blue-500 border border-blue-600 rounded-r-md hover:bg-blue-600 transition duration-150">
                            ❯
                        </a>
                    @else
                        <span
                            class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-gray-400 bg-gray-300 border border-gray-400 cursor-not-allowed rounded-r-md">
                            ❯
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
