@extends('adminlte::page')

@section('content_header')
<nav aria-label="breadcrumb" style="font-size: 18pt">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/productos') }}">Productos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Datos de los Productos</li>
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
               
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="producto">Producto <b style="color: red">(*)</b></label>
                                <select name="producto_id" class="form-control" readonly >
                                  
                                        <option value="{{ $producto->id }}" {{ old('producto_id') == $producto->id ? 'selected' : '' }}>
                                            {{ $producto->nombre }}
                                        </option>
                                   
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="codigo">Código <b style="color: red">(*)</b></label>
                                <input type="text" class="form-control" name="codigo" value="{{$producto->codigo }}" readonly>
                                @error('codigo') <small style="color:red">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nombre">Nombre <b style="color: red">(*)</b></label>
                                <input type="text" class="form-control" name="nombre" value="{{$producto->nombre  }}" readonly>
                                @error('nombre') <small style="color:red">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="precio_venta">Precio Venta <b style="color: red">(*)</b></label>
                                <input type="number" step="0.01" class="form-control" name="precio_venta" value="{{ $producto->precio_venta  }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock">Stock <b style="color: red">(*)</b></label>
                                <input type="number" class="form-control" name="stock" value="{{ $producto->stock }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea class="form-control" name="descripcion" rows="3" readonly>{{ $producto->descripcion  }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            
                        </div>
                    </div>
                    <hr>
                    <a href="{{ url('/admin/productos') }}" class="btn btn-secondary">Volver</a>
                    
                
            </div>
        </div>
    </div>
</div>
@stop