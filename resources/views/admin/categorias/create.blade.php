@extends('adminlte::page')

@section('title', 'Nueva Categoría')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark font-weight-bold">
        <i class="fas fa-plus-circle text-primary mr-2"></i>Nueva Categoría
    </h1>
    <a href="{{ url('/admin/categorias') }}" class="btn btn-secondary font-weight-bold shadow-sm">
        <i class="fas fa-arrow-left mr-1"></i> Volver al listado
    </a>
</div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 col-sm-12">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-tags mr-2 text-primary"></i>Datos de la Categoría
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ url('/admin/categorias/create') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="nombre">
                            <i class="fas fa-tag mr-1 text-muted"></i>
                            Nombre <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-tags"></i></span>
                            </div>
                            <input type="text" id="nombre" name="nombre"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   placeholder="Ej: Cócteles, Nevera, Insumos..."
                                   value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">
                            <i class="fas fa-align-left mr-1 text-muted"></i>
                            Descripción <small class="text-muted">(opcional)</small>
                        </label>
                        <textarea class="form-control" name="descripcion" id="descripcion" rows="3"
                                  placeholder="Ingrese una breve descripción de la categoría">{{ old('descripcion') }}</textarea>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted"><span class="text-danger">*</span> Campos obligatorios</small>
                        <div>
                            <a href="{{ url('/admin/categorias') }}" class="btn btn-secondary mr-2">
                                <i class="fas fa-times mr-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary font-weight-bold">
                                <i class="fas fa-save mr-1"></i> Guardar Categoría
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
