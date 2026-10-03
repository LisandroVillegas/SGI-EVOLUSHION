@extends('adminlte::page')

@section('title', 'Cerrar Turno')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark font-weight-bold">
        <i class="fas fa-lock text-warning mr-2"></i>Cierre de Turno
    </h1>
    <a href="{{ url('/admin/turnos') }}" class="btn btn-secondary font-weight-bold shadow-sm">
        <i class="fas fa-arrow-left mr-1"></i> Volver a Turnos
    </a>
</div>
@stop

@section('content')
<form action="{{ url('/admin/turnos/'.$turno->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        {{-- COLUMNA IZQUIERDA: RESUMEN FINANCIERO Y ARQUEO --}}
        <div class="col-lg-4 col-md-5">
            <div class="card card-outline card-warning shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-cash-register mr-1 text-warning"></i> Arqueo de Caja</h3>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-primary btn-block mb-3 font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalVentaOlvidada">
                        <i class="fas fa-cart-plus mr-1"></i> Registrar Venta Olvidada
                    </button>

                    <div class="p-2 bg-light rounded border mb-3">
                        <small class="text-muted d-block">Cajero Responsable</small>
                        <strong class="text-dark">{{ $turno->user->name }}</strong>
                    </div>

                    {{-- Indicadores Numéricos con Ventas en Efectivo --}}
                    <div class="row text-center mb-2">
                        <div class="col-4 pr-1">
                            <div class="p-2 bg-light border rounded">
                                <small class="text-muted d-block">Base Inicial</small>
                                <span class="font-weight-bold" style="font-size: 0.85rem;">${{ number_format($turno->base_caja, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="col-4 px-1">
                            <div class="p-2 bg-light border rounded">
                                <small class="text-success d-block">(+) Ventas</small>
                                <span class="font-weight-bold text-success" id="texto_total_ventas" style="font-size: 0.85rem;">${{ number_format($totalVentasEfectivo, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="col-4 pl-1">
                            <div class="p-2 bg-light border rounded">
                                <small class="text-danger d-block">(-) Compras</small>
                                <span class="font-weight-bold text-danger" id="texto_total_compras" style="font-size: 0.85rem;">${{ number_format($totalCompras, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    @if($totalFiadoCobrado > 0)
                        <div class="alert alert-info py-1 px-2 mb-2 text-center small">
                            <i class="fas fa-hand-holding-usd mr-1"></i> (+) Cobro de Fiados en Caja: <strong>${{ number_format($totalFiadoCobrado, 0, ',', '.') }}</strong>
                        </div>
                    @endif

                    {{-- Campo de Pago a Trabajadora --}}
                    <div class="form-group mb-3">
                        <label for="pago_trabajadora" class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-user-minus text-danger mr-1"></i> (-) Pago a Trabajadora / Sueldo
                        </label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold bg-white border-danger text-danger">$</span>
                            </div>
                            <input type="number" step="1" min="0" name="pago_trabajadora" id="pago_trabajadora" 
                                   class="form-control border-danger font-weight-bold text-danger" 
                                   value="{{ old('pago_trabajadora', intval($pagoTrabajadora)) }}" placeholder="0">
                        </div>
                    </div>

                    {{-- Campo de Base para el Siguiente Turno (Sin decimales) --}}
                    <div class="form-group mb-3">
                        <label for="base_siguiente_turno" class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-wallet text-primary mr-1"></i> (-) Base para el Siguiente Turno
                        </label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold bg-white border-primary text-primary">$</span>
                            </div>
                            <input type="number" step="1" min="0" name="base_siguiente_turno" id="base_siguiente_turno" 
                                   class="form-control border-primary font-weight-bold text-primary" 
                                   value="{{ old('base_siguiente_turno', 0) }}" placeholder="0">
                        </div>
                        <small class="text-muted" style="font-size: 0.75rem;">Dinero que se dejará en caja para arrancar el próximo turno.</small>
                    </div>

                    {{-- Dinero Esperado en Caja --}}
                    <div class="alert alert-warning text-center mb-3 p-2 shadow-sm border-warning">
                        <small class="text-uppercase font-weight-bold d-block text-dark">Dinero Esperado en Caja</small>
                        <h3 class="m-0 font-weight-bold text-dark" id="texto_dinero_esperado">${{ number_format($dineroEsperado, 0, ',', '.') }}</h3>
                        <input type="hidden" name="dinero_esperado" id="dinero_esperado_caja" value="{{ $dineroEsperado }}" data-valor="{{ $dineroEsperado }}">
                    </div>

                    {{-- Campo de Entrada Principal --}}
                    <div class="form-group mb-3">
                        <label for="total_efectivo_real" class="font-weight-bold text-dark">
                            <i class="fas fa-coins text-success mr-1"></i> Efectivo Real Contado <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold bg-white border-success text-success">$</span>
                            </div>
                            <input type="number" step="1" min="0" name="total_efectivo_real" id="total_efectivo_real" 
                                   class="form-control form-control-lg border-success font-weight-bold text-success @error('total_efectivo_real') is-invalid @enderror" 
                                   placeholder="0" required style="font-size: 1.5rem;">
                        </div>
                    </div>

                    <hr>

                    {{-- Reportes Organizados en Pestañas --}}
                    <div class="card card-tabs card-outline card-secondary mb-0 shadow-none border">
                        <div class="card-header p-0 pt-1 border-bottom-0 bg-light">
                            <ul class="nav nav-tabs" id="tabs-reporte" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active py-1 px-2 font-weight-bold" id="tab-cierre" data-toggle="pill" href="#content-cierre" role="tab"><small><i class="fas fa-calculator mr-1"></i> Cierre</small></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-1 px-2 font-weight-bold" id="tab-apertura" data-toggle="pill" href="#content-apertura" role="tab"><small><i class="fas fa-history mr-1"></i> Apertura y Bitácora</small></a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-2">
                            <div class="tab-content" id="tabs-reporte-content">
                                <div class="tab-pane fade show active" id="content-cierre" role="tabpanel">
                                    <textarea name="reporte_descuadre_cierre" id="reporte_descuadre_cierre" rows="6" 
                                              class="form-control form-control-sm bg-light" readonly style="resize: none; font-size: 9pt;"></textarea>
                                </div>
                                <div class="tab-pane fade" id="content-apertura" role="tabpanel">
                                    <div class="form-control form-control-sm bg-light" style="height: auto; min-height: 120px; white-space: pre-line; font-size: 9pt; overflow-y: auto;">{{ $turno->observaciones ?? 'Sin novedades al abrir.' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3 mb-0">
                        <label for="notas_cajero" class="small font-weight-bold text-dark"><i class="fas fa-pen mr-1"></i> Observaciones Adicionales del Cierre:</label>
                        <textarea name="notas_cajero" id="notas_cajero" rows="2" class="form-control form-control-sm" 
                                  placeholder="Escribe notas adicionales sobre dinero o novedades..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: CONTEO DE PRODUCTOS (Estilo Calcado de Apertura) --}}
        <div class="col-lg-8 col-md-7">
            <div class="card card-outline card-primary shadow-sm border rounded">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold text-dark m-0">
                        <i class="fas fa-boxes mr-2 text-primary"></i>Conteo de Inventario Final
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-valign-middle m-0">
                            <thead class="bg-light text-muted border-bottom">
                                <tr class="small text-uppercase font-weight-bold">
                                    <th class="py-3 pl-3">Producto / Insumo</th>
                                    <th class="text-center py-3">Stock Inicial</th>
                                    <th class="text-center py-3">Esperado</th>
                                    <th class="text-center py-3" style="width: 170px;">Conteo Físico Real</th>
                                    <th class="text-center py-3">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($turno->detalles as $index => $detalle)
                                    <tr>
                                        <td class="align-middle pl-3 font-weight-bold text-dark">
                                            {{ $detalle->producto ? $detalle->producto->nombre . ' (' . ($detalle->producto->categoria->nombre ?? 'Sin Categoría') . ')' : 'Producto no encontrado' }}
                                            <input type="hidden" name="productos[{{ $index }}][id]" value="{{ $detalle->producto_id }}">
                                        </td>
                                        <td class="text-center align-middle font-weight-normal text-muted">{{ $detalle->stock_fisico_apertura }}</td>
                                        {{-- Stock Esperado idéntico a Apertura --}}
                                        <td class="text-center align-middle" id="esperado_{{ $detalle->producto_id }}">
                                            <span class="border rounded px-2 py-1 bg-white font-weight-bold text-dark" style="font-size: 0.9rem;">
                                                {{ $detalle->stock_esperado_calculado }}
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="input-group input-group-sm mx-auto" style="max-width: 130px;">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="btn btn-outline-secondary btn-restar" data-id="{{ $detalle->producto_id }}"><i class="fas fa-minus"></i></button>
                                                </div>
                                                <input type="number" min="0" name="productos[{{ $index }}][stock_fisico]" 
                                                       id="input_stock_{{ $detalle->producto_id }}" 
                                                       class="form-control text-center font-weight-bold input-stock-fisico border-secondary" 
                                                       value="{{ old('productos.'.$index.'.stock_fisico', $detalle->stock_esperado_calculado) }}" 
                                                       data-id="{{ $detalle->producto_id }}"
                                                       data-esperado="{{ $detalle->stock_esperado_calculado }}"
                                                       data-nombre="{{ $detalle->producto->nombre }}">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary btn-sumar" data-id="{{ $detalle->producto_id }}"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle" id="badge_estado_{{ $detalle->producto_id }}">
                                            <span class="badge badge-success px-2 py-1 font-weight-bold">0</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Pie de página con Botón Cancelar Gris Claro Nítido --}}
                <div class="card-footer bg-light text-right py-3 border-top">
                    <a href="{{ route('turnos.index') }}" class="btn btn-secondary text-white font-weight-bold mr-2 px-4 shadow-sm">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-warning font-weight-bold shadow-sm px-4">
                        <i class="fas fa-lock mr-1"></i> Finalizar y Cerrar Turno
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- MODAL VENTA OLVIDADA --}}
<div class="modal fade" id="modalVentaOlvidada" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-cart-plus mr-1"></i> Registrar Venta Olvidada</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info py-2 px-3 mb-3 small">
                    <i class="fas fa-info-circle mr-1"></i>
                    Usa este formulario para registrar una venta que se realizó pero <strong>no se marcó en el POS</strong>. Descuenta stock y suma al dinero del turno.
                </div>
                <form id="formVentaOlvidada">
                    @csrf
                    <input type="hidden" name="turno_id" value="{{ $turno->id }}">

                    <div class="form-group">
                        <label for="producto_olvidado_id">Producto Vendido <span class="text-danger">*</span></label>
                        <select name="producto_id" id="producto_olvidado_id" class="form-control" required>
                            <option value="">Seleccione un producto...</option>
                            @foreach($turno->detalles as $det)
                                <option value="{{ $det->producto_id }}" 
                                        data-precio="{{ $det->producto->precio_venta }}"
                                        data-categoria="{{ $det->producto->categoria_id }}">
                                    {{ $det->producto->nombre }} ({{ $det->producto->categoria->nombre ?? 'Sin Categoría' }}) (${{ number_format($det->producto->precio_venta, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cantidad_olvidada">Cantidad Vendida <span class="text-danger">*</span></label>
                        <input type="number" name="cantidad" id="cantidad_olvidada" class="form-control" min="1" max="999" value="1" required>
                    </div>

                    {{-- Switch de Promoción --}}
                    <div class="form-group mb-3 bg-white p-2 border rounded">
                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input" id="switchPromocionOlvidada" name="aplica_promocion" value="1">
                            <label class="custom-control-label font-weight-bold text-dark cursor-pointer" for="switchPromocionOlvidada">
                                <i class="fas fa-tags text-warning mr-1"></i> Aplicar Promociones
                            </label>
                        </div>
                    </div>

                    {{-- Cuadro de Resumen de Totales y Descuento --}}
                    <div class="bg-light rounded border p-2 mb-3">
                        <div class="d-flex justify-content-between align-items-center small text-muted mb-1" id="box-descuento-olvidada" style="display: none !important;">
                            <span>Descuento Promocional:</span>
                            <span class="font-weight-bold text-danger" id="texto-descuento-olvidada">-$0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h6 font-weight-bold m-0 text-muted">TOTAL A COBRAR:</span>
                            <span class="h4 font-weight-bold m-0 text-success" id="texto-total-olvidada">$0</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Método de Pago <span class="text-danger">*</span></label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons" id="grupo-metodo-olvidada">
                            <label class="btn btn-outline-success active font-weight-bold">
                                <input type="radio" name="metodo_pago" value="efectivo" checked> <i class="fas fa-money-bill-wave mr-1"></i> Efectivo
                            </label>
                            <label class="btn btn-outline-primary font-weight-bold">
                                <input type="radio" name="metodo_pago" value="transferencia"> <i class="fas fa-mobile-alt mr-1"></i> Transferencia
                            </label>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label for="motivo_olvidado">Observación / Motivo</label>
                        <input type="text" name="motivo" id="motivo_olvidado" class="form-control"
                               placeholder="Ej: No se marcó en caja por afán" maxlength="255">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary font-weight-bold" id="btnGuardarVentaOlvidada">
                    <i class="fas fa-save mr-1"></i> Guardar Venta
                </button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    const promocionesRegistradas = @json(\App\Models\Promocion::where('estado', true)->get());

    $(document).ready(function() {
        let baseInicial = {{ $turno->base_caja }};
        let totalVentas = {{ $totalVentasEfectivo }};
        let totalCompras = {{ $totalCompras }};
        let totalFiadoCobrado = {{ $totalFiadoCobrado }};
        let egresosTexto = @json($detalleEgresosTexto ?? []);
        let notasTurno = @json($turno->observaciones ?? '');

        function recalcularFinanzas() {
            let pagoTrabajadora = parseFloat($('#pago_trabajadora').val()) || 0;
            let baseSiguiente = parseFloat($('#base_siguiente_turno').val()) || 0;

            let dineroEsperadoCalculado = (baseInicial + totalVentas + totalFiadoCobrado) - totalCompras - pagoTrabajadora - baseSiguiente;
            
            $('#dinero_esperado_caja').val(dineroEsperadoCalculado);
            $('#texto_dinero_esperado').text('$' + new Intl.NumberFormat('es-CO').format(dineroEsperadoCalculado));

            generarReporteUnificado();
        }

        function generarReporteUnificado() {
            let fmt = new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 });
            let pagoTrabajadora = parseFloat($('#pago_trabajadora').val()) || 0;
            let baseSiguiente = parseFloat($('#base_siguiente_turno').val()) || 0;
            let dineroEsperado = parseFloat($('#dinero_esperado_caja').val()) || 0;
            let efectivoReal = parseFloat($('#total_efectivo_real').val());
            
            let lineasReporte = [];
            lineasReporte.push("RESUMEN FINANCIERO DEL TURNO:");
            lineasReporte.push("--------------------------------");
            lineasReporte.push("(+) Base Inicial: $" + fmt.format(baseInicial));
            lineasReporte.push("(+) Ventas en Efectivo: $" + fmt.format(totalVentas));
            if (totalFiadoCobrado > 0) {
                lineasReporte.push("(+) Abonos a Fiados (Caja): $" + fmt.format(totalFiadoCobrado));
            }
            lineasReporte.push("(-) Compras / Egresos: $" + fmt.format(totalCompras));
            if (pagoTrabajadora > 0) {
                lineasReporte.push("(-) Pago a Trabajadora / Sueldo: $" + fmt.format(pagoTrabajadora));
            }
            if (baseSiguiente > 0) {
                lineasReporte.push("(-) Base para el Siguiente Turno: $" + fmt.format(baseSiguiente));
            }
            lineasReporte.push("--------------------------------");
            lineasReporte.push("(=) DINERO ESPERADO EN CAJA: $" + fmt.format(dineroEsperado));
            
            if (!isNaN(efectivoReal)) {
                lineasReporte.push("(=) EFECTIVO REAL CONTADO: $" + fmt.format(efectivoReal));
                lineasReporte.push("");
                
                if (dineroEsperado < 0) {
                    lineasReporte.push("ADVERTENCIA: Los egresos y sueldos superan el efectivo disponible en caja.\n");
                }

                let difEfectivo = efectivoReal - dineroEsperado;
                if (difEfectivo <= -0.01) {
                    lineasReporte.push("- FALTANTE DE DINERO EN CAJA: -$" + fmt.format(Math.abs(difEfectivo)));
                } else if (difEfectivo >= 0.01) {
                    lineasReporte.push("- SOBRANTE DE DINERO EN CAJA: +$" + fmt.format(difEfectivo));
                } else {
                    lineasReporte.push("- CAJA CUADRADA CORRECTAMENTE");
                }
            }
            
            lineasReporte.push("\nDETALLE DE INVENTARIO Y EGRESOS:");
            let hayNovedades = false;
            
            if (egresosTexto.length > 0) {
                egresosTexto.forEach(function(eg) { 
                    lineasReporte.push(eg); 
                    hayNovedades = true;
                });
            }
            
            $('.input-stock-fisico').each(function() {
                let esperado = parseInt($(this).data('esperado')) || 0;
                let fisico = parseInt($(this).val()) || 0;
                let dif = fisico - esperado;
                let nombre = $(this).data('nombre');
                
                if (dif < 0) {
                    lineasReporte.push("- FALTANTE EN CIERRE: " + nombre + " (" + Math.abs(dif) + " und)");
                    hayNovedades = true;
                } else if (dif > 0) {
                    lineasReporte.push("- SOBRANTE EN CIERRE: " + nombre + " (+" + dif + " und)");
                    hayNovedades = true;
                }
            });

            if (!hayNovedades) {
                lineasReporte.push("- Sin novedades de inventario ni egresos.");
            }

            if (notasTurno && notasTurno.trim() !== '') {
                lineasReporte.push("\nREGISTROS Y NOVEDADES DEL TURNO:");
                lineasReporte.push(notasTurno.trim());
            }

            $('#reporte_descuadre_cierre').val(lineasReporte.join("\n"));
        }

        function calcularModalOlvidada() {
            let select = $('#producto_olvidado_id option:selected');
            let productoId = parseInt($('#producto_olvidado_id').val());
            let categoriaId = parseInt(select.data('categoria'));
            let precio = parseFloat(select.data('precio')) || 0;
            let cantidad = parseInt($('#cantidad_olvidada').val()) || 1;

            if (!productoId || precio <= 0) {
                $('#box-descuento-olvidada').attr('style', 'display: none !important;');
                $('#texto-total-olvidada').text('$0');
                return;
            }

            let subtotal = precio * cantidad;
            let descuentoTotal = 0;

            if ($('#switchPromocionOlvidada').is(':checked')) {
                let promo = promocionesRegistradas.find(p => p.producto_id == productoId);

                if (!promo && categoriaId) {
                    promo = promocionesRegistradas.find(p => !p.producto_id && p.categoria_id == categoriaId);
                }

                if (promo && cantidad >= promo.cantidad_minima && promo.cantidad_minima > 0) {
                    let veces = Math.floor(cantidad / promo.cantidad_minima);
                    descuentoTotal = veces * parseFloat(promo.descuento);
                }
            }

            if (descuentoTotal > 0) {
                $('#texto-descuento-olvidada').text('-$' + descuentoTotal.toLocaleString('es-CO'));
                $('#box-descuento-olvidada').removeAttr('style');
            } else {
                $('#box-descuento-olvidada').attr('style', 'display: none !important;');
            }

            let totalFinal = Math.max(0, subtotal - descuentoTotal);
            $('#texto-total-olvidada').text('$' + totalFinal.toLocaleString('es-CO'));
        }

        $('#producto_olvidado_id, #cantidad_olvidada, #switchPromocionOlvidada').on('change keyup input', function() {
            calcularModalOlvidada();
        });

        $('#pago_trabajadora, #base_siguiente_turno, #total_efectivo_real').on('input change', function() {
            recalcularFinanzas();
        });

        $('.input-stock-fisico').on('input change', function() {
            let id = $(this).data('id');
            let esperado = parseInt($(this).data('esperado')) || 0;
            let fisico = parseInt($(this).val()) || 0;
            let dif = fisico - esperado;

            let badgeCell = $('#badge_estado_' + id);
            if (dif === 0) {
                badgeCell.html('<span class="badge badge-success px-2 py-1 font-weight-bold">0</span>');
            } else if (dif < 0) {
                badgeCell.html('<span class="badge badge-danger px-2 py-1 font-weight-bold">' + dif + '</span>');
            } else {
                badgeCell.html('<span class="badge badge-info px-2 py-1 font-weight-bold">+' + dif + '</span>');
            }

            generarReporteUnificado();
        });

        $('.btn-restar').click(function() {
            let id = $(this).data('id');
            let input = $('#input_stock_' + id);
            let val = parseInt(input.val()) || 0;
            if (val > 0) {
                input.val(val - 1).trigger('change');
            }
        });

        $('.btn-sumar').click(function() {
            let id = $(this).data('id');
            let input = $('#input_stock_' + id);
            let val = parseInt(input.val()) || 0;
            input.val(val + 1).trigger('change');
        });

        $('#btnGuardarVentaOlvidada').click(function() {
            if (!$('#producto_olvidado_id').val()) {
                Swal.fire('Campo requerido', 'Debes seleccionar un producto.', 'warning');
                return;
            }
            if (parseInt($('#cantidad_olvidada').val()) < 1) {
                Swal.fire('Campo requerido', 'La cantidad debe ser al menos 1.', 'warning');
                return;
            }

            let btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...');

            let formData = $('#formVentaOlvidada').serialize();
            $.ajax({
                url: "{{ route('turnos.registrarVentaOlvidada') }}",
                type: "POST",
                data: formData,
                success: function(res) {
                    if (res.success) {
                        $('#modalVentaOlvidada').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: '¡Venta registrada!',
                            html: '<strong>' + res.cantidad + 'x ' + res.producto_nombre + '</strong><br>Total: <strong>$' + new Intl.NumberFormat('es-CO').format(res.subtotal) + '</strong>',
                            confirmButtonText: 'Aceptar'
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire('No se pudo registrar', res.message || 'Error inesperado al guardar la venta.', 'error');
                        btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Guardar Venta');
                    }
                },
                error: function(xhr) {
                    let msg = 'Ocurrió un error inesperado. Intente nuevamente.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error del servidor', msg, 'error');
                    btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Guardar Venta');
                }
            });
        });

        $('#modalVentaOlvidada').on('hidden.bs.modal', function() {
            $('#formVentaOlvidada')[0].reset();
            $('#box-descuento-olvidada').hide();
            $('#texto-total-olvidada').text('$0');
            $('#grupo-metodo-olvidada label').removeClass('active');
            $('#grupo-metodo-olvidada label:first').addClass('active');
        });

        recalcularFinanzas();
        calcularModalOlvidada();
    });
</script>
@stop