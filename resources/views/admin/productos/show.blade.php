@extends('adminlte::page')

@section('title', 'Detalle del Producto')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark font-weight-bold">
        <i class="fas fa-box text-primary mr-2"></i>Detalle del Producto
    </h1>
    <a href="{{ url('/admin/productos') }}" class="btn btn-secondary font-weight-bold shadow-sm">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Productos
    </a>
</div>
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