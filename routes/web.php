<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\AdminController::class, 'index'])->name('home')->middleware('auth');
Route::get('/admin', [App\Http\Controllers\AdminController::class,'index'])->name('admin.index')->middleware('auth');


//Rutas para categorias

Route::get('/admin/categorias',[App\Http\Controllers\CategoriaController::class, 'index'])->name('categoria.index')
->middleware('auth');

Route::get('/admin/categorias/create',[App\Http\Controllers\CategoriaController::class, 'create'])->name('categoria.create')
->middleware('auth');

Route::post('/admin/categorias/create',[App\Http\Controllers\CategoriaController::class, 'store'])->name('categoria.store')
->middleware('auth');

Route::get('/admin/categoria/{id}',[App\Http\Controllers\CategoriaController::class, 'show'])->name('categoria.show')
->middleware('auth');

Route::get('/admin/categoria/{id}/edit',[App\Http\Controllers\CategoriaController::class, 'edit'])->name('categoria.edit')
->middleware('auth');

Route::put('/admin/categoria/{id}',[App\Http\Controllers\CategoriaController::class, 'update'])->name('categoria.update')
->middleware('auth');

Route::delete('/admin/categoria/{id}',[App\Http\Controllers\CategoriaController::class, 'destroy'])->name('categoria.destroy')
->middleware('auth');



//Rutas para productos

Route::get('/admin/productos', [App\Http\Controllers\ProductoController::class, 'index'])->name('producto.index')
    ->middleware('auth');

Route::get('/admin/productos/create', [App\Http\Controllers\ProductoController::class, 'create'])->name('producto.create')
    ->middleware('auth');

Route::post('/admin/productos', [App\Http\Controllers\ProductoController::class, 'store'])->name('producto.store')
    ->middleware('auth');

Route::get('/admin/producto/{id}', [App\Http\Controllers\ProductoController::class, 'show'])->name('producto.show')
    ->middleware('auth');

Route::get('/admin/producto/{id}/edit', [App\Http\Controllers\ProductoController::class, 'edit'])->name('producto.edit')
    ->middleware('auth');

Route::put('/admin/producto/{id}', [App\Http\Controllers\ProductoController::class, 'update'])->name('producto.update')
    ->middleware('auth');

Route::delete('/admin/producto/{id}', [App\Http\Controllers\ProductoController::class, 'destroy'])->name('producto.destroy')
    ->middleware('auth');

// Rutas para Compras


Route::get('/admin/compras', [App\Http\Controllers\CompraController::class, 'index'])->name('compras.index')
    ->middleware('auth');

Route::get('/admin/compras/create', [App\Http\Controllers\CompraController::class, 'create'])->name('compras.create')
    ->middleware('auth');

Route::post('/admin/compras', [App\Http\Controllers\CompraController::class, 'store'])->name('compras.store')
    ->middleware('auth');

Route::get('/admin/compras/{id}', [App\Http\Controllers\CompraController::class, 'show'])->name('compras.show')
    ->middleware('auth');

Route::get('/admin/compras/{id}/edit', [App\Http\Controllers\CompraController::class, 'edit'])->name('compras.edit')
    ->middleware('auth');

Route::put('/admin/compras/{id}', [App\Http\Controllers\CompraController::class, 'update'])->name('compras.update')
    ->middleware('auth');

Route::delete('/admin/compras/{id}', [App\Http\Controllers\CompraController::class, 'destroy'])->name('compras.destroy')
    ->middleware('auth');


// Rutas Para Turnos

Route::get('/admin/turnos', [App\Http\Controllers\TurnoController::class, 'index'])->name('turnos.index')
    ->middleware('auth');

Route::get('/admin/turnos/create', [App\Http\Controllers\TurnoController::class, 'create'])->name('turnos.create')
    ->middleware('auth');

Route::post('/admin/turnos', [App\Http\Controllers\TurnoController::class, 'store'])->name('turnos.store')
    ->middleware('auth');

Route::get('/admin/turnos/{id}', [App\Http\Controllers\TurnoController::class, 'show'])->name('turnos.show')
    ->middleware('auth');

Route::get('/admin/turnos/{id}/edit', [App\Http\Controllers\TurnoController::class, 'edit'])->name('turnos.edit')
    ->middleware('auth');

Route::put('/admin/turnos/{id}', [App\Http\Controllers\TurnoController::class, 'update'])->name('turnos.update')
    ->middleware('auth');

Route::delete('/admin/turnos/{id}', [App\Http\Controllers\TurnoController::class, 'destroy'])->name('turnos.destroy')
    ->middleware('auth');

// Ruta Para Ventas Olvidadas duerante el turno

Route::post('/admin/turnos/registrar-venta-olvidada', [App\Http\Controllers\TurnoController::class, 'registrarVentaOlvidada'])
    ->name('turnos.registrarVentaOlvidada')
     ->middleware('auth');


// Rutas para Ventas / POS

Route::get('/admin/ventas', [App\Http\Controllers\VentaController::class, 'index'])->name('ventas.index')
    ->middleware('auth');

Route::get('/admin/ventas/create', [App\Http\Controllers\VentaController::class, 'create'])->name('ventas.create')
    ->middleware('auth');

Route::post('/admin/ventas', [App\Http\Controllers\VentaController::class, 'store'])->name('ventas.store')
    ->middleware('auth');

Route::get('/admin/ventas/{id}', [App\Http\Controllers\VentaController::class, 'show'])->name('ventas.show')
    ->middleware('auth');

Route::delete('/admin/ventas/{id}', [App\Http\Controllers\VentaController::class, 'destroy'])->name('ventas.destroy')
    ->middleware('auth');

    //RUTAS PARA FIADOS
  Route::patch('/admin/ventas/{id}/pagar-fiado', [App\Http\Controllers\VentaController::class, 'pagarFiado'])
    ->name('ventas.pagarFiado')
    ->middleware('auth');
    
// Rutas para Reportes Diarios
    Route::get('/admin/reportes/diario', [App\Http\Controllers\ReporteController::class, 'index'])->name('reportes.diario');
    Route::get('/admin/reportes/diario/pdf', [App\Http\Controllers\ReporteController::class, 'exportarPdf'])->name('reportes.diario.pdf');
