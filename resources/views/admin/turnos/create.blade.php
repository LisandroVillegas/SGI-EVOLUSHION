@extends('adminlte::page')

@section('content_header')
<nav aria-label="breadcrumb" style="font-size: 18pt">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/turnos') }}">Turnos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Abrir Turno</li>
  </ol>
</nav>
<hr>
@stop

@section('content')
<form action="{{ url('/admin/turnos') }}" method="POST" id="form-abrir-turno">
    @csrf
    <div class="row">
        {{-- Tarjeta 1: Base de Caja y Observaciones --}}
        <div class="col-md-4">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-cash-register mr-1 text-primary"></i> Información de Caja</h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark mb-1">Cajero Responsable</label>
                        <input type="text" class="form-control bg-light font-weight-bold text-dark" value="{{ Auth::user()->name }}" readonly>
                    </div>

                    {{-- Base de Caja Inicial (Formato entero sin decimales, por defecto 0 si no hay base anterior) --}}
                    <div class="form-group mb-3">
                        <label for="base_caja" class="font-weight-bold text-dark mb-1">
                            Base de Caja Inicial <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold bg-white text-dark">$</span>
                            </div>
                            <input type="number" step="1" min="0" name="base_caja" id="base_caja" 
                                   class="form-control font-weight-bold text-dark @error('base_caja') is-invalid @enderror" 
                                   placeholder="0" value="{{ old('base_caja', intval($baseSugerida ?? $baseSiguiente ?? 0)) }}" required autofocus>
                        </div>
                        @error('base_caja')
                            <small class="text-danger font-weight-bold">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Novedades automáticas de inventario --}}
                    <div class="form-group mb-3">
                        <label for="novedades_sistema" class="small font-weight-bold text-dark">
                            Reporte Automático de Inventario
                            <small class="text-muted d-block font-weight-normal">(Generado según diferencias físicas)</small>
                        </label>
                        <textarea name="reporte_inventario" id="novedades_sistema" rows="4" class="form-control form-control-sm bg-light" readonly style="resize: none; font-size: 9pt;">Apertura de turno sin novedades en inventario.</textarea>
                    </div>

                    {{-- Campo libre de observaciones opcionales --}}
                    <div class="form-group mb-0">
                        <label for="notas" class="small font-weight-bold text-dark">
                            Observaciones / Novedades Adicionales <span class="text-muted font-weight-normal">(Opcional)</span>
                        </label>
                        <textarea name="notas" id="notas" rows="3" class="form-control form-control-sm @error('notas') is-invalid @enderror" 
                                  placeholder="Escribe aquí cualquier otra observación adicional...">{{ old('notas') }}</textarea>
                        @error('notas')
                            <small class="text-danger font-weight-bold">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjeta 2: Conteo Físico de Productos --}}
        <div class="col-md-8">
            <div class="card card-outline card-info shadow-sm border rounded">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark m-0">
                        <i class="fas fa-boxes mr-2 text-info"></i>Arqueo Físico de Inventario Inicial
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-valign-middle m-0">
                            <thead class="bg-light text-muted border-bottom">
                                <tr class="text-uppercase small font-weight-bold">
                                    <th class="border-0 pl-3 py-3">Producto / Insumo</th>
                                    <th class="text-center border-0 py-3" style="width: 130px;">Stock Sistema</th>
                                    <th class="text-center border-0 py-3" style="width: 170px;">Conteo Físico Real</th>
                                    <th class="text-center border-0 py-3 pr-3" style="width: 110px;">Diferencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productos as $producto)
                                    <tr class="fila-conteo">
                                        <td class="align-middle pl-3 font-weight-bold text-dark">
                                            <strong class="text-dark d-block">{{ $producto->nombre }}</strong>
                                            <input type="hidden" name="productos[{{ $loop->index }}][id]" value="{{ $producto->id }}">
                                            <input type="hidden" name="productos[{{ $loop->index }}][stock_sistema]" value="{{ $producto->stock }}">
                                        </td>
                                        {{-- Stock Sistema repintado limpio y legible --}}
                                        <td class="text-center align-middle">
                                            <span class="border rounded px-2 py-1 bg-white font-weight-bold text-dark" style="font-size: 0.95rem;">
                                                {{ $producto->stock }}
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="input-group input-group-sm mx-auto" style="max-width: 130px;">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="btn btn-outline-secondary btn-restar-stock"><i class="fas fa-minus"></i></button>
                                                </div>
                                                <input type="number" name="productos[{{ $loop->index }}][stock_fisico]" 
                                                       class="form-control text-center font-weight-bold input-conteo stock-fisico px-1 border-secondary" 
                                                       data-nombre="{{ $producto->nombre }}"
                                                       data-sistema="{{ $producto->stock }}" 
                                                       value="{{ old('productos.'.$loop->index.'.stock_fisico', $producto->stock) }}" min="0" required>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary btn-sumar-stock"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle pr-3">
                                            <span class="badge badge-success span-diferencia px-2 py-1 font-weight-bold" style="font-size: 10pt; min-width: 40px;">0</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Pie de página con botón Cancelar gris sólido visible --}}
                <div class="card-footer bg-light text-right py-3 border-top">
                    <a href="{{ url('/admin/turnos') }}" class="btn btn-secondary text-white font-weight-bold px-4 mr-2 shadow-sm">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow-sm">
                        <i class="fas fa-key mr-1"></i> Abrir Turno con Arqueo
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Botón sumar stock
        $('.btn-sumar-stock').on('click', function() {
            let input = $(this).closest('tr').find('.stock-fisico');
            let val = parseInt(input.val()) || 0;
            input.val(val + 1).trigger('input');
        });

        // Botón restar stock
        $('.btn-restar-stock').on('click', function() {
            let input = $(this).closest('tr').find('.stock-fisico');
            let val = parseInt(input.val()) || 0;
            if (val > 0) {
                input.val(val - 1).trigger('input');
            }
        });

        // Función para actualizar el recuadro informativo del sistema
        function actualizarReporteApertura() {
            let novedades = [];

            $('.stock-fisico').each(function() {
                let fila = $(this).closest('tr');
                let nombreProducto = $(this).data('nombre');
                let stockSistema = parseInt($(this).attr('data-sistema')) || 0;
                let stockFisico = parseInt($(this).val()) || 0;
                let diferencia = stockFisico - stockSistema;
                
                let badge = fila.find('.span-diferencia');

                if (diferencia === 0) {
                    fila.removeClass('table-danger table-warning');
                    badge.removeClass('badge-danger badge-warning badge-info').addClass('badge-success').text('0');
                } else if (diferencia < 0) {
                    fila.removeClass('table-warning').addClass('table-danger');
                    badge.removeClass('badge-success badge-warning badge-info').addClass('badge-danger').text(diferencia);
                    novedades.push("FALTANTE: " + nombreProducto + " (" + Math.abs(diferencia) + " und)");
                } else {
                    fila.removeClass('table-danger').addClass('table-warning');
                    badge.removeClass('badge-success badge-danger badge-warning').addClass('badge-info').text('+' + diferencia);
                    novedades.push("SOBRANTE: " + nombreProducto + " (+" + diferencia + " und)");
                }
            });

            if (novedades.length > 0) {
                $('#novedades_sistema').val("ALERTAS DE INVENTARIO:\n- " + novedades.join("\n- "));
            } else {
                $('#novedades_sistema').val("Apertura de turno sin novedades en inventario.");
            }
        }

        // Evento cuando cambian los valores de los inputs manuales o con botones
        $(document).on('input change', '.stock-fisico', function() {
            actualizarReporteApertura();
        });

        // Ejecutar al cargar
        actualizarReporteApertura();
    });
</script>
@stop