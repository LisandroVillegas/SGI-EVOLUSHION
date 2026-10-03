@extends('adminlte::page')

@section('title', 'Nuevo Producto')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark font-weight-bold">
        <i class="fas fa-plus-circle text-primary mr-2"></i>Nuevo Producto
    </h1>
    <a href="{{ url('/admin/productos') }}" class="btn btn-secondary font-weight-bold shadow-sm">
        <i class="fas fa-arrow-left mr-1"></i> Volver al listado
    </a>
</div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-md-12">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-box mr-2 text-primary"></i>Datos del Producto
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ url('/admin/productos') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="categoria_id">
                                    <i class="fas fa-tags mr-1 text-muted"></i>
                                    Categoría <span class="text-danger">*</span>
                                </label>
                                <select name="categoria_id" id="categoria_id"
                                        class="form-control @error('categoria_id') is-invalid @enderror" required>
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categoria_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="codigo">
                                    <i class="fas fa-barcode mr-1 text-muted"></i>
                                    Código <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="codigo" name="codigo"
                                       class="form-control @error('codigo') is-invalid @enderror"
                                       placeholder="Ej: COC-001"
                                       value="{{ old('codigo') }}" required>
                                @error('codigo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nombre">
                                    <i class="fas fa-signature mr-1 text-muted"></i>
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="nombre" name="nombre"
                                       class="form-control @error('nombre') is-invalid @enderror"
                                       placeholder="Ej: Coctel 10K"
                                       value="{{ old('nombre') }}" required>
                                @error('nombre') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="precio_venta">
                                    <i class="fas fa-dollar-sign mr-1 text-muted"></i>
                                    Precio de Venta <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" step="0.01" id="precio_venta" name="precio_venta"
                                           class="form-control @error('precio_venta') is-invalid @enderror"
                                           placeholder="0"
                                           value="{{ old('precio_venta') }}" required>
                                </div>
                                @error('precio_venta') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock">
                                    <i class="fas fa-cubes mr-1 text-muted"></i>
                                    Stock Inicial <span class="text-danger">*</span>
                                </label>
                                <input type="number" id="stock" name="stock"
                                       class="form-control @error('stock') is-invalid @enderror"
                                       placeholder="0"
                                       value="{{ old('stock', 0) }}" required>
                                @error('stock') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="descripcion">
                                    <i class="fas fa-align-left mr-1 text-muted"></i>
                                    Descripción <small class="text-muted">(opcional)</small>
                                </label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                          placeholder="Descripción breve del producto...">{{ old('descripcion') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted"><span class="text-danger">*</span> Campos obligatorios</small>
                        <div>
                            <a href="{{ url('/admin/productos') }}" class="btn btn-secondary mr-2">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary font-weight-bold">
                                <i class="fas fa-save mr-1"></i> Guardar Producto
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop