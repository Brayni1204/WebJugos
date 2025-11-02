<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Empresa;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Obtener la empresa y pasar el logo a todas las vistas
        /* $empresa = Empresa::first();
        $logo = $empresa ? asset('storage/' . $empresa->favicon_url) : asset('vendor/adminlte/dist/img/LogoPrincipal.png');

        View::share('logo_img', $logo); */
    }
}
