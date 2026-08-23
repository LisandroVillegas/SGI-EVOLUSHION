@extends('adminlte::page')

@section('content_header')
<nav aria-label="breadcrumb" style="font-size: 18pt">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/productos') }}">Productos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Creación de Productos</li>
  </ol>
</nav>
<hr>
@stop

@section('content')
<div class="row">
    <div class="col-md-9">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Llenar Datos del Formulario</h3>
            </div>
            <div class="card-body">
                <form action="{{ url('/admin/productos') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="categoria_id">Categoría <b style="color: red">(*)</b></label>
                                <select name="categoria_id" class="form-control" required>
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="codigo">Código <b style="color: red">(*)</b></label>
                                <input type="text" class="form-control" name="codigo" value="{{ old('codigo') }}" required>
                                @error('codigo') <small style="color:red">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nombre">Nombre <b style="color: red">(*)</b></label>
                                <input type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required>
                                @error('nombre') <small style="color:red">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="precio_venta">Precio Venta <b style="color: red">(*)</b></label>
                                <input type="number" step="0.01" class="form-control" name="precio_venta" value="{{ old('precio_venta') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock">Stock <b style="color: red">(*)</b></label>
                                <input type="number" class="form-control" name="stock" value="{{ old('stock', 0) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="imagen">Imagen</label>
                                <input type="file" class="form-control-file" name="imagen">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <a href="{{ url('/admin/productos') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop