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



