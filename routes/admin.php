<?php

use App\Http\Controllers\Admin\CajaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\CreandoNuevosPedidosController;
use App\Http\Controllers\Admin\CreandoNuevosPedidosDetalleController;
use App\Http\Controllers\Admin\EmpresaController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\Admin\MesaController;
use App\Http\Controllers\Admin\PaginaController;
use App\Http\Controllers\Admin\ParrafoController;
use App\Http\Controllers\admin\ProductosController;
use App\Http\Controllers\Admin\SubtituloController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VentaController;

Route::get('', [HomeController::class, 'index'])->middleware('can:admin.home')->name('admin.home');

Route::resource('users', UserController::class)->only('index', 'edit', 'update')->middleware('can:admin.users.index')->names('admin.users');
Route::resource('categoria', CategoriaController::class)->middleware('can:admin.categoria.index')->names('admin.categoria');

Route::resource('producto', ProductosController::class)->middleware('can:admin.productos.index')->names('admin.producto');

Route::resource('pagina', PaginaController::class)->middleware('can:admin.paginas.index')->names('admin.paginas');
Route::resource('subtitulo', SubtituloController::class)->middleware('can:admin.subtitulos.index')->names('admin.subtitulos');
Route::resource('parrafo', ParrafoController::class)->middleware('can:admin.parrafos.index')->names('admin.parrafos');

Route::resource('empresa', EmpresaController::class)->middleware('can:admin.empresa.index')->names('admin.empresa');
Route::resource('mesas', MesaController::class)->names('admin.mesas');
Route::patch('mesas/{mesa}/toggle-status', [MesaController::class, 'toggleStatus'])->name('admin.mesas.toggle-status');




Route::get('reportes', [CajaController::class, 'index'])->name('admin.reportes.index');
Route::get('reportes/diario', [CajaController::class, 'reporteDiario']);
Route::get('reportes/semanal', [CajaController::class, 'reporteSemanal']);
Route::get('reportes/mensual', [CajaController::class, 'reporteMensual']); 
Route::get('reportes/rango', [CajaController::class, 'reportePorRango']);

// Nuevas rutas para los reportes detallados
Route::get('reportes/ventas-por-dia', [CajaController::class, 'ventasPorDia'])->name('admin.reportes.ventasPorDia');
Route::get('reportes/frecuencia-clientes', [CajaController::class, 'frecuenciaClientes'])->name('admin.reportes.frecuenciaClientes');
Route::get('reportes/productos-mas-vendidos', [CajaController::class, 'productosMasVendidos'])->name('admin.reportes.productosMasVendidos');
Route::get('reportes/reporte-ventas', [CajaController::class, 'reporteVentas'])->name('admin.reportes.reporteVentas');



Route::resource('nuevopedidoadmin', CreandoNuevosPedidosController::class)->names('admin.nuevospedidosadmin');
Route::post('/pedidos/{pedido}/cambiar-estado', [CreandoNuevosPedidosController::class, 'cambiarEstado'])
    ->name('pedidos.cambiarEstado');

Route::get('pedidos/{id}/comprobante', [CreandoNuevosPedidosController::class, 'obtenerComprobante'])->name('admin.nuevospedidosadmin.comprobante');
Route::get('pedidos/{id}/comprobantedetalle', [CreandoNuevosPedidosController::class, 'obtenerComprobanteDetalle'])->name('admin.nuevospedidosadmin.comprobantedetalle');

Route::resource('nuevodetallepedido', CreandoNuevosPedidosDetalleController::class)->names('admin.nuevospedidosdetalleadmin');
Route::get('nuevodetallepedido/{pedidoId}/detalles', [CreandoNuevosPedidosDetalleController::class, 'obtenerDetallesPedido'])->name('admin.nuevospedidosdetalleadmin.detalles');

// ... (dentro de tu grupo de rutas de administrador)
Route::get('/nuevopedido/actualizar-tabla', [App\Http\Controllers\Admin\CreandoNuevosPedidosController::class, 'actualizarTabla'])->name('admin.nuevospedidos.actualizarTabla');

/* Route::get('pedidos/{pedido}/comprobante', [CreandoNuevosPedidosController::class, 'generarComprobante'])->name('admin.pedidos.generarComprobante'); */
Route::post('pedidos/{pedido}/completar', [CreandoNuevosPedidosController::class, 'completarPedido'])->name('admin.pedidos.completar');
Route::post('pedidos/{pedido}/cancelar', [CreandoNuevosPedidosController::class, 'cancelarPedido'])->middleware('auth')->name('admin.pedidos.cancelar');

Route::resource('ventas', VentaController::class)->middleware('can:admin.ventas.index')->names('admin.ventas');
