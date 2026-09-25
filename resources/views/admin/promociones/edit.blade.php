@extends('adminlte::page')

@section('title', 'Editar Promoción')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-edit text-info"></i> Editar Promoción</h1>
        <a href="{{ route('promociones.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
@stop

@section('content')
    <div class="card card-outline card-info">
        <div class="card-header">
            <h3 class="card-title">Modificar Promoción: {{ $promocion->nombre }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('promociones.update', $promocion->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="nombre">Nombre de la Promoción <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $promocion->nombre) }}" required>
                        @error('nombre')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label for="categoria_id">Categoría que aplica <span class="text-danger">*</span></label>
                        <select name="categoria_id" class="form-control @error('categoria_id') is-invalid @enderror" required>
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ old('categoria_id', $promocion->categoria_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="cantidad_minima">Cantidad Mínima <span class="text-danger">*</span></label>
                        <input type="number" name="cantidad_minima" class="form-control @error('cantidad_minima') is-invalid @enderror" value="{{ old('cantidad_minima', $promocion->cantidad_minima) }}" min="1" required>
                        @error('cantidad_minima')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="descuento">Monto a Descontar ($) <span class="text-danger">*</span></label>
                        <input type="number" name="descuento" class="form-control @error('descuento') is-invalid @enderror" value="{{ old('descuento', $promocion->descuento) }}" min="0" step="100" required>
                        @error('descuento')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- En su lugar, si la columna 'estado' sigue existiendo en la BD, enviamos siempre 1 --}}
                    <input type="hidden" name="estado" value="1">
                </div>

                <hr>
                <div class="text-right">
                    <a href="{{ route('promociones.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-info"><i class="fas fa-sync-alt"></i> Actualizar Promoción</button>
                </div>
            </form>
        </div>
    </div>
@stop