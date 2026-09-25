<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\PromocionController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ConfiguracionController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Panel Principal
Route::get('/home', [AdminController::class, 'index'])->name('home')->middleware('auth');
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index')->middleware('auth');


// Rutas para Categorías
Route::get('/admin/categorias', [CategoriaController::class, 'index'])->name('categoria.index')->middleware('auth');
Route::get('/admin/categorias/create', [CategoriaController::class, 'create'])->name('categoria.create')->middleware('auth');
Route::post('/admin/categorias/create', [CategoriaController::class, 'store'])->name('categoria.store')->middleware('auth');
Route::get('/admin/categoria/{id}', [CategoriaController::class, 'show'])->name('categoria.show')->middleware('auth');
Route::get('/admin/categoria/{id}/edit', [CategoriaController::class, 'edit'])->name('categoria.edit')->middleware('auth');
Route::put('/admin/categoria/{id}', [CategoriaController::class, 'update'])->name('categoria.update')->middleware('auth');
Route::delete('/admin/categoria/{id}', [CategoriaController::class, 'destroy'])->name('categoria.destroy')->middleware(['auth', 'verify.pin']);


// Rutas para Productos
Route::get('/admin/productos', [ProductoController::class, 'index'])->name('producto.index')->middleware('auth');
Route::get('/admin/productos/create', [ProductoController::class, 'create'])->name('producto.create')->middleware('auth');
Route::post('/admin/productos', [ProductoController::class, 'store'])->name('producto.store')->middleware('auth');
Route::get('/admin/producto/{id}', [ProductoController::class, 'show'])->name('producto.show')->middleware('auth');
Route::get('/admin/producto/{id}/edit', [ProductoController::class, 'edit'])->name('producto.edit')->middleware('auth');
Route::put('/admin/producto/{id}', [ProductoController::class, 'update'])->name('producto.update')->middleware('auth');
Route::delete('/admin/producto/{id}', [ProductoController::class, 'destroy'])->name('producto.destroy')->middleware(['auth', 'verify.pin']);


// Rutas para Compras
Route::get('/admin/compras', [CompraController::class, 'index'])->name('compras.index')->middleware('auth');
Route::get('/admin/compras/create', [CompraController::class, 'create'])->name('compras.create')->middleware('auth');
Route::post('/admin/compras', [CompraController::class, 'store'])->name('compras.store')->middleware('auth');
Route::get('/admin/compras/{id}', [CompraController::class, 'show'])->name('compras.show')->middleware('auth');
Route::get('/admin/compras/{id}/edit', [CompraController::class, 'edit'])->name('compras.edit')->middleware('auth');
Route::put('/admin/compras/{id}', [CompraController::class, 'update'])->name('compras.update')->middleware('auth');
Route::delete('/admin/compras/{id}', [CompraController::class, 'destroy'])->name('compras.destroy')->middleware(['auth', 'verify.pin']);

// Rutas para compras rapidas
Route::post('/compras/rapida', [CompraController::class, 'storeRapida'])->name('compras.storeRapida')->middleware('auth');
Route::resource('compras', CompraController::class)->middleware('auth');


// Rutas para Turnos
Route::get('/admin/turnos', [TurnoController::class, 'index'])->name('turnos.index')->middleware('auth');
Route::get('/admin/turnos/create', [TurnoController::class, 'create'])->name('turnos.create')->middleware('auth');
Route::post('/admin/turnos', [TurnoController::class, 'store'])->name('turnos.store')->middleware('auth');
Route::get('/admin/turnos/{id}', [TurnoController::class, 'show'])->name('turnos.show')->middleware('auth');
Route::get('/admin/turnos/{id}/edit', [TurnoController::class, 'edit'])->name('turnos.edit')->middleware('auth');
Route::put('/admin/turnos/{id}', [TurnoController::class, 'update'])->name('turnos.update')->middleware('auth');
Route::delete('/admin/turnos/{id}', [TurnoController::class, 'destroy'])->name('turnos.destroy')->middleware(['auth', 'verify.pin']);
Route::post('/admin/turnos/registrar-venta-olvidada', [TurnoController::class, 'registrarVentaOlvidada'])->name('turnos.registrarVentaOlvidada')->middleware('auth');


// Rutas para Ventas / POS
Route::get('/admin/ventas', [VentaController::class, 'index'])->name('ventas.index')->middleware('auth');
Route::get('/admin/ventas/create', [VentaController::class, 'create'])->name('ventas.create')->middleware('auth');
Route::post('/admin/ventas', [VentaController::class, 'store'])->name('ventas.store')->middleware('auth');
Route::get('/admin/ventas/{id}', [VentaController::class, 'show'])->name('ventas.show')->middleware('auth');
Route::patch('/admin/ventas/{id}/pagar-fiado', [VentaController::class, 'pagarFiado'])->name('ventas.pagarFiado')->middleware('auth');
Route::delete('/admin/ventas/{id}', [VentaController::class, 'destroy'])->name('ventas.destroy')->middleware(['auth', 'verify.pin']);


// Rutas para Promociones
Route::get('/admin/promociones', [PromocionController::class, 'index'])->name('promociones.index')->middleware('auth');
Route::get('/admin/promociones/create', [PromocionController::class, 'create'])->name('promociones.create')->middleware('auth');
Route::post('/admin/promociones', [PromocionController::class, 'store'])->name('promociones.store')->middleware('auth');
Route::get('/admin/promociones/{id}/edit', [PromocionController::class, 'edit'])->name('promociones.edit')->middleware('auth');
Route::put('/admin/promociones/{id}', [PromocionController::class, 'update'])->name('promociones.update')->middleware('auth');
Route::patch('/admin/promociones/{id}/toggle-estado', [PromocionController::class, 'toggleEstado'])->name('promociones.toggle')->middleware('auth');
Route::delete('/admin/promociones/{id}', [PromocionController::class, 'destroy'])->name('promociones.destroy')->middleware(['auth', 'verify.pin']);


// Rutas para Reportes Diarios
Route::get('/admin/reportes/diario', [ReporteController::class, 'index'])->name('reportes.diario')->middleware('auth');
Route::get('/admin/reportes/diario/pdf', [ReporteController::class, 'exportarPdf'])->name('reportes.diario.pdf')->middleware('auth');

//Rutas para Configuraciones
Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index')->middleware('auth');
Route::post('/configuracion/update-pin', [ConfiguracionController::class, 'updatePin'])->name('configuracion.updatePin')->middleware('auth');


