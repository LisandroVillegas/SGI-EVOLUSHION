@extends('adminlte::page')

@section('title', 'Crear Promoción')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-plus-circle text-primary"></i> Crear Nueva Promoción</h1>
        <a href="{{ route('promociones.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Datos de la Promoción</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('promociones.store') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Nombre de la Promoción -->
                    <div class="col-md-6 form-group">
                        <label for="nombre">Nombre de la Promoción <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Ej: Promo 2x$4.000 en Cócteles" required>
                        @error('nombre')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Categoría que Aplica -->
                    <div class="col-md-6 form-group">
                        <label for="categoria_id">Categoría que aplica <span class="text-danger">*</span></label>
                        <select name="categoria_id" id="categoria_id" class="form-control @error('categoria_id') is-invalid @enderror" required>
                            <option value="">-- Selecciona una Categoría --</option>
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat->id }}" data-nombre="{{ strtolower($cat->nombre) }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Producto Específico (Solo para Nevera) -->
                    <div class="col-md-6 form-group d-none" id="box_producto">
                        <label for="producto_id">Producto Específico de Nevera <span class="text-danger">*</span></label>
                        <select name="producto_id" id="producto_id" class="form-control @error('producto_id') is-invalid @enderror">
                            <option value="">-- Selecciona un Producto --</option>
                            @foreach ($productos as $prod)
                                <option value="{{ $prod->id }}" data-categoria="{{ $prod->categoria_id }}" class="opcion-producto" {{ old('producto_id') == $prod->id ? 'selected' : '' }}>
                                    {{ $prod->nombre }} ({{ $prod->categoria->nombre ?? 'Sin Categoría' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Debes seleccionar el producto específico al que aplicará la promoción en la Nevera.</small>
                        @error('producto_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Cantidad Mínima -->
                    <div class="col-md-3 form-group">
                        <label for="cantidad_minima">Cantidad Mínima <span class="text-danger">*</span></label>
                        <input type="number" name="cantidad_minima" class="form-control @error('cantidad_minima') is-invalid @enderror" value="{{ old('cantidad_minima', 2) }}" min="1" required>
                        <small class="form-text text-muted">Ej: Si pones 2, aplica por cada par de productos comprados.</small>
                        @error('cantidad_minima')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Monto a Descontar -->
                    <div class="col-md-3 form-group">
                        <label for="descuento">Monto a Descontar ($) <span class="text-danger">*</span></label>
                        <input type="number" name="descuento" class="form-control @error('descuento') is-invalid @enderror" value="{{ old('descuento') }}" placeholder="Ej: 4000" min="0" step="100" required>
                        <small class="form-text text-muted">Monto a restar por cada combo/grupo completado.</small>
                        @error('descuento')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <hr>
                <div class="text-right">
                    <a href="{{ route('promociones.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Promoción</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        function evaluarCategoria() {
            let selectedOption = $('#categoria_id').find('option:selected');
            let nombreCategoria = selectedOption.data('nombre') || '';
            let categoriaId = $('#categoria_id').val();

            let boxProducto = $('#box_producto');
            let selectProducto = $('#producto_id');

            if (nombreCategoria.includes('nevera')) {
                boxProducto.removeClass('d-none');
                selectProducto.prop('required', true);

                // Filtrar las opciones para mostrar solo productos de la categoría seleccionada
                $('.opcion-producto').each(function() {
                    let catProd = $(this).data('categoria');
                    if (catProd == categoriaId) {
                        $(this).removeClass('d-none');
                    } else {
                        $(this).addClass('d-none');
                    }
                });
            } else {
                boxProducto.addClass('d-none');
                selectProducto.prop('required', false);
                selectProducto.val('');
            }
        }

        // Detectar cambios en la categoría
        $('#categoria_id').change(function() {
            evaluarCategoria();
        });

        // Ejecutar al cargar la página por si hay valores previos de validación (old)
        evaluarCategoria();
    });
</script>
@stop