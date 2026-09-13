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
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cash-register mr-1"></i> Información de Caja</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Cajero Responsable</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="base_caja">Base de Caja Inicial <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" step="100" min="0" name="base_caja" id="base_caja" 
                                   class="form-control @error('base_caja') is-invalid @enderror" 
                                   placeholder="Ej: 50000" value="{{ old('base_caja') }}" required autofocus>
                        </div>
                        @error('base_caja')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Novedades automáticas de inventario --}}
                    <div class="form-group">
                        <label for="novedades_sistema">
                            Reporte Automático de Inventario
                            <small class="text-muted d-block">(Generado según diferencias físicas)</small>
                        </label>
                        <textarea id="novedades_sistema" rows="4" class="form-control bg-light" readonly>Apertura de turno sin novedades en inventario.</textarea>
                    </div>

                    {{-- Campo libre de observaciones opcionales (Sin required) --}}
                    <div class="form-group">
                        <label for="notas">
                            Observaciones / Novedades Adicionales <span class="text-muted font-weight-normal">(Opcional)</span>
                        </label>
                        <textarea name="notas" id="notas" rows="3" class="form-control @error('notas') is-invalid @enderror" 
                                  placeholder="Escribe aquí cualquier otra observación adicional...">{{ old('notas') }}</textarea>
                        @error('notas')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjeta 2: Conteo Físico de Productos --}}
        <div class="col-md-8">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-boxes mr-1"></i> Arqueo Físico de Inventario Inicial</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-uppercase small font-weight-bold text-muted">
                                    <th class="border-0 pl-3">Producto / Insumo</th>
                                    <th class="text-center border-0" style="width: 110px;">Stock Sistema</th>
                                    <th class="text-center border-0" style="width: 160px;">Conteo Físico Real</th>
                                    <th class="text-center border-0 pl-2" style="width: 100px;">Diferencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productos as $producto)
                                    <tr class="fila-conteo">
                                        <td class="align-middle pl-3">
                                            <strong class="text-dark d-block">{{ $producto->nombre }}</strong>
                                            <input type="hidden" name="productos[{{ $loop->index }}][id]" value="{{ $producto->id }}">
                                            <input type="hidden" name="productos[{{ $loop->index }}][stock_sistema]" value="{{ $producto->stock }}">
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-light border px-2 py-1" style="font-size: 10pt;">{{ $producto->stock }}</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="input-group input-group-sm mx-auto" style="max-width: 130px;">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="btn btn-outline-secondary btn-restar-stock"><i class="fas fa-minus"></i></button>
                                                </div>
                                                <input type="number" name="productos[{{ $loop->index }}][stock_fisico]" 
                                                       class="form-control form-control-sm text-center font-weight-bold input-conteo stock-fisico px-1" 
                                                       data-nombre="{{ $producto->nombre }}"
                                                       data-sistema="{{ $producto->stock }}" 
                                                       value="{{ $producto->stock }}" min="0" required>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary btn-sumar-stock"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle pr-3">
                                            <span class="badge badge-success span-diferencia" style="font-size: 10.5pt; width: 45px;">0</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light text-right py-2">
                    <a href="{{ url('/admin/turnos') }}" class="btn btn-secondary px-4">Cancelar</a>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4"><i class="fas fa-key mr-1"></i> Abrir Turno con Arqueo</button>
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