<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Empresa;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Listeners\CreateClienteForNewUser;
use Illuminate\Foundation\AliasLoader;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->booting(function() {
            $loader = AliasLoader::getInstance();
            $loader->alias('Cloudinary', \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Event::listen(
            Registered::class,
            CreateClienteForNewUser::class
        );
        // Obtener la empresa y pasar el logo a todas las vistas
        /* $empresa = Empresa::first();
        $logo = $empresa ? asset('storage/' . $empresa->favicon_url) : asset('vendor/adminlte/dist/img/LogoPrincipal.png');

        View::share('logo_img', $logo); */
    }
}
