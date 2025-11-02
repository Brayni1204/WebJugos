<x-guest-layout>
    <x-authentication-card>
        @php
            $empresa = \App\Models\Empresa::latest()->first();
            $icono =
                $empresa && $empresa->favicon_url
                    ? asset('storage/' . $empresa->favicon_url)
                    : asset('default-favicon.ico');
        @endphp
        <x-slot name="logo">
            <img class="h-16 sm:h16 w-auto" src="{{ $icono }}" alt="Logo">
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" autofocus />
            </div>

            <div class="flex justify-end mt-4">
                <x-button class="ms-4">
                    {{ __('Confirm') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
