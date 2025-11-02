<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        // Aquí es donde defines tus rutas personalizadas
        then: function () {
            Route::middleware('web', 'auth') // Aplica los middlewares necesarios
                ->prefix('admin')           // Define el prefijo 'admin'
                ->namespace('App\Http\Controllers\admin')
                ->group(base_path('routes/admin.php')); // Carga tu archivo de rutas
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
