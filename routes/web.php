<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PaginaController;
use App\Http\Controllers\ParrafoController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\ControllerStripeCarritoMesa;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\NosotrosController;
use App\Http\Controllers\NuevosPedidosController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ReservarController;
use App\Http\Controllers\StripeController;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\PagoMercadoController;

Route::get('/pedido-detalle-actualizado/{pedido_id}', [NuevosPedidosController::class, 'obtenerDetallesParaCliente'])->name('pedido.detalles.cliente');


Route::post('/pedidos/pagar/{pedido}', [PedidoController::class, 'procesarPago'])->name('pedido.pagar');
Route::get('/pedidos/pago-exitoso/{pedido}', [PedidoController::class, 'pagoExitoso'])->name('pago.exitoso');


Route::post('/pedidos-mesa/pagar-mesa/{pedido}', [NuevosPedidosController::class, 'procesarPagoMesa'])->name('pedidoMesa.pagar');
Route::get('/pedidos-mesa/pago-mesa-exitoso/{pedido}', [NuevosPedidosController::class, 'pagoExitosoMesa'])->name('pagoMesa.exitoso');


Route::get('/', [CategoriaController::class, 'index'])->name('views.index');

Route::get('/reservar', [ReservarController::class, 'mostrarReservar'])->name('views.reservar');

Route::post('/pedidocarrito/agregar', [ReservarController::class, 'agregarAlCarrito'])->name('carrito.agregar');
Route::post('/pedidocarrito/eliminar', [ReservarController::class, 'eliminarDelCarrito'])->name('carrito.eliminar');
Route::post('/pedidocarrito/vaciar', [ReservarController::class, 'vaciarCarrito'])->name('carrito.vaciar');

Route::post('/reservar-pedidos', [NuevosPedidosController::class, 'store'])->name('pedidos.store');
Route::get('/pedido/ver', [NuevosPedidosController::class, 'verPedido'])->name('pedido.ver');
Route::get('/pedido-detalle', [ReservarController::class, 'verPedido'])->name('views.pedido_detalle');
Route::post('/pedidos-detalle/{id}/actualizar', [NuevosPedidosController::class, 'actualizarPedido'])->name('pedidos.actualizar');




Route::get('/pedidos', [PedidoController::class, 'verpedido'])->middleware('auth')->name('views.pedidos');
Route::get('/{pedido}/comprobante', [PedidoController::class, 'generarComprobante'])->name('views.pedidos.generarComprobante');

Route::get('/productos', [ProductosController::class, 'productos'])->name('views.productos');
Route::get('/nosotros', [NosotrosController::class, 'index'])->name('views.nosotros');


Route::get('/categoria/{id}/productos', [ProductosController::class, 'index'])->name('views.categorias');
Route::get('/{pagina}', [PaginaController::class, 'pagina'])->name('views.pagina');



Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.index');
    })->name('dashboard');
});


Route::get('/carrito/ver', [CarritoController::class, 'verCarrito'])->name('carrito.ver');
Route::post('/carrito/agregar', [CarritoController::class, 'agregarAlCarrito'])->name('pagecarrito.agregar');

Route::get('/carrito/incrementar/{id}', [CarritoController::class, 'incrementarCantidad'])->name('carrito.incrementarcantidad');
Route::get('/carrito/decrementar/{id}', [CarritoController::class, 'decrementarCantidad'])->name('carrito.decrementarcantidad');

Route::post('/carrito/eliminar', [CarritoController::class, 'eliminarDelCarrito'])->name('pagecarrito.eliminar');
Route::post('/carrito/vaciar', [CarritoController::class, 'vaciarCarrito'])->name('pagecarrito.vaciar');
Route::post('/pedido/realizar', [CarritoController::class, 'realizarPedido'])->name('pedido.realizar');

Route::get('/{pagina}/{subtitulo}', [ParrafoController::class, 'parrafo'])->name('views.parrafo');

// En routes/web.php
