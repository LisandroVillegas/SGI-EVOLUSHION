@extends('adminlte::page')

@section('content_header')
<nav aria-label="breadcrumb" style="font-size: 16pt">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/turnos') }}">Turnos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Cerrar Turno</li>
  </ol>
</nav>
<hr class="mt-0">
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
                    <h3 class="card-title font-weight-bold"><i class="fas fa-cash-register mr-1 text-warning"></i> Arqueo de Caja</h3>
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

                    {{-- Campo de Pago a Trabajadora --}}
                    <div class="form-group mb-3">
                        <label for="pago_trabajadora" class="font-weight-bold text-dark mb-1">
                            <i class="fas fa-user-minus text-danger mr-1"></i> (-) Pago a Trabajadora / Sueldo
                        </label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold bg-white border-danger text-danger">$</span>
                            </div>
                            <input type="number" step="100" min="0" name="pago_trabajadora" id="pago_trabajadora" 
                                   class="form-control border-danger font-weight-bold text-danger" 
                                   value="{{ old('pago_trabajadora', $pagoTrabajadora) }}" placeholder="0">
                        </div>
                    </div>

                    {{-- Dinero Esperado Destacado (Con el name agregado) --}}
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
                            <input type="number" step="100" min="0" name="total_efectivo_real" id="total_efectivo_real" 
                                   class="form-control form-control-lg border-success font-weight-bold text-success @error('total_efectivo_real') is-invalid @enderror" 
                                   placeholder="0" required autofocus style="font-size: 1.5rem;">
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
                                    <a class="nav-link py-1 px-2 font-weight-bold" id="tab-apertura" data-toggle="pill" href="#content-apertura" role="tab"><small><i class="fas fa-history mr-1"></i> Apertura</small></a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-2">
                            <div class="tab-content" id="tabs-reporte-content">
                                <div class="tab-pane fade show active" id="content-cierre" role="tabpanel">
                                    <textarea name="reporte_descuadre_cierre" id="reporte_descuadre_cierre" rows="5" 
                                              class="form-control form-control-sm bg-light" readonly style="resize: none; font-size: 9pt;"></textarea>
                                </div>
                                <div class="tab-pane fade" id="content-apertura" role="tabpanel">
                                    <textarea class="form-control form-control-sm bg-light" rows="5" readonly style="resize: none; font-size: 9pt;">{{ $turno->notas ?? 'Sin novedades al abrir.' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3 mb-0">
                        <label for="notas_cajero" class="small font-weight-bold"><i class="fas fa-pen mr-1"></i> Observaciones del Cierre:</label>
                        <textarea name="notas_cajero" id="notas_cajero" rows="2" class="form-control form-control-sm" 
                                  placeholder="Escribe notas adicionales sobre dinero o novedades..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: TABLA DE PRODUCTOS E INVENTARIO --}}
        <div class="col-lg-8 col-md-7">
            <div class="card card-outline card-warning shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold m-0"><i class="fas fa-boxes mr-1 text-warning"></i> Control de Stock (Apertura vs Cierre)</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-uppercase small font-weight-bold text-muted">
                                    <th class="border-0 pl-3">Producto</th>
                                    <th class="text-center border-0" style="width: 110px;">Apertura</th>
                                    <th class="text-center border-0" style="width: 110px;">Esperado</th>
                                    <th class="text-center border-0" style="width: 160px;">Conteo Cierre</th>
                                    <th class="text-center border-0 pl-2" style="width: 100px;">Diferencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($turno->detalles as $detalle)
                                    @php
                                        $stockEsperado = $detalle->stock_esperado_calculado ?? $detalle->stock_fisico_apertura; 
                                    @endphp
                                    <tr id="fila-producto-{{ $detalle->producto_id }}" class="fila-conteo">
                                        <td class="align-middle pl-3">
                                            <strong class="text-dark d-block">{{ $detalle->producto->nombre }}</strong>
                                            <input type="hidden" name="productos[{{ $loop->index }}][id]" value="{{ $detalle->producto_id }}">
                                            <input type="hidden" name="productos[{{ $loop->index }}][stock_esperado]" id="input_esperado_{{ $detalle->producto_id }}" value="{{ $stockEsperado }}">
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-light border px-2 py-1" style="font-size: 10pt;">{{ $detalle->stock_fisico_apertura }}</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-secondary px-2 py-1 badge-stock-esperado" id="badge_esperado_{{ $detalle->producto_id }}" style="font-size: 10pt;">{{ $stockEsperado }}</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="input-group input-group-sm mx-auto" style="max-width: 130px;">
                                                <div class="input-group-prepend">
                                                    <button type="button" class="btn btn-outline-secondary btn-restar-stock" data-id="{{ $detalle->producto_id }}"><i class="fas fa-minus"></i></button>
                                                </div>
                                                <input type="number" name="productos[{{ $loop->index }}][stock_fisico]" 
                                                       class="form-control form-control-sm text-center font-weight-bold input-conteo-cierre px-1" 
                                                       data-producto-id="{{ $detalle->producto_id }}"
                                                       data-nombre="{{ $detalle->producto->nombre }}"
                                                       data-esperado="{{ $stockEsperado }}" 
                                                       value="{{ $stockEsperado }}" min="0" required>
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-outline-secondary btn-sumar-stock" data-id="{{ $detalle->producto_id }}"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle pr-3">
                                            <span class="badge badge-success px-2 py-1 span-diferencia-cierre" style="font-size: 10.5pt; width: 45px;">0</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light text-right py-2">
                    <a href="{{ url('/admin/turnos') }}" class="btn btn-secondary px-4">Cancelar</a>
                    <button type="submit" class="btn btn-warning font-weight-bold px-4"><i class="fas fa-lock mr-1"></i> Finalizar y Cerrar Turno</button>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- MODAL VENTA OLVIDADA --}}
<div class="modal fade" id="modalVentaOlvidada" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-cart-plus mr-1"></i> Registrar Venta Olvidada</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formVentaOlvidada">
                    <input type="hidden" name="turno_id" value="{{ $turno->id }}">
                    
                    <div class="form-group">
                        <label for="select_producto" class="font-weight-bold">Producto <span class="text-danger">*</span></label>
                        <select name="producto_id" id="select_producto" class="form-control" required>
                            <option value="">-- Seleccionar Producto --</option>
                            @foreach($turno->detalles as $detalle)
                                <option value="{{ $detalle->producto_id }}">{{ $detalle->producto->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="input_cantidad" class="font-weight-bold">Cantidad <span class="text-danger">*</span></label>
                        <input type="number" name="cantidad" id="input_cantidad" class="form-control" value="1" min="1" required>
                    </div>

                    <div class="form-group mb-0">
                        <label for="input_motivo" class="font-weight-bold">Motivo / Observación</label>
                        <input type="text" name="motivo" id="input_motivo" class="form-control" placeholder="Ej: Olvido de registro en el sistema">
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary font-weight-bold" id="btnGuardarVentaOlvidada">
                    <i class="fas fa-save mr-1"></i> Guardar y Ajustar Caja
                </button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        let ventasOlvidadas = [];
        let totalComprasTurno = parseFloat("{{ $totalCompras }}") || 0;
        let totalVentasEfectivoTurno = parseFloat("{{ $totalVentasEfectivo }}") || 0;
        let baseCaja = parseFloat("{{ $turno->base_caja }}") || 0;
        let listaCompras = @json($detalleComprasTexto ?? []);

        $('.btn-sumar-stock').on('click', function() {
            let input = $(this).closest('tr').find('.input-conteo-cierre');
            let val = parseInt(input.val()) || 0;
            input.val(val + 1).trigger('change');
        });

        $('.btn-restar-stock').on('click', function() {
            let input = $(this).closest('tr').find('.input-conteo-cierre');
            let val = parseInt(input.val()) || 0;
            if (val > 0) {
                input.val(val - 1).trigger('change');
            }
        });

        function recalcularDineroEsperado() {
            let pagoTrabajadora = parseFloat($('#pago_trabajadora').val()) || 0;
            let nuevoDineroEsperado = (baseCaja + totalVentasEfectivoTurno) - totalComprasTurno - pagoTrabajadora;

            $('#dinero_esperado_caja').data('valor', nuevoDineroEsperado).val(nuevoDineroEsperado);
            $('#texto_dinero_esperado').text('$' + nuevoDineroEsperado.toLocaleString('es-CO'));

            actualizarReporteCierre();
        }

        function actualizarReporteCierre() {
            let novedades = [];
            let pagoTrabajadora = parseFloat($('#pago_trabajadora').val()) || 0;

            if (totalVentasEfectivoTurno > 0) {
                novedades.push("VENTAS REGISTRADAS POS (EFECTIVO): +$" + totalVentasEfectivoTurno.toLocaleString('es-CO'));
            }

            if (listaCompras.length > 0) {
                novedades.push("COMPRAS REALIZADAS EN TURNO (Total: -$" + totalComprasTurno.toLocaleString('es-CO') + "):");
                listaCompras.forEach(function(item) {
                    novedades.push("   * " + item);
                });
            }

            if (pagoTrabajadora > 0) {
                novedades.push("PAGO A TRABAJADORA: -$" + pagoTrabajadora.toLocaleString('es-CO'));
            }

            if (ventasOlvidadas.length > 0) {
                novedades.push("VENTAS NO REGISTRADAS (AGREGADAS EN CIERRE):");
                ventasOlvidadas.forEach(function(item) {
                    novedades.push("   * " + item);
                });
            }

            let dineroEsperado = parseFloat($('#dinero_esperado_caja').data('valor')) || 0;
            let efectivoInput = $('#total_efectivo_real').val();

            if (efectivoInput !== '' && efectivoInput !== undefined) {
                let efectivoReal = parseFloat(efectivoInput) || 0;
                let difDinero = efectivoReal - dineroEsperado;

                if (difDinero < 0) {
                    novedades.push("FALTANTE DE DINERO EN CAJA: -$" + Math.abs(difDinero).toLocaleString('es-CO'));
                } else if (difDinero > 0) {
                    novedades.push("SOBRANTE DE DINERO EN CAJA: +$" + difDinero.toLocaleString('es-CO'));
                }
            }

            $('.input-conteo-cierre').each(function() {
                let fila = $(this).closest('tr');
                let nombreProducto = $(this).data('nombre');
                let stockEsperado = parseInt($(this).attr('data-esperado')) || 0;
                let stockFisico = parseInt($(this).val()) || 0;
                let diferencia = stockFisico - stockEsperado;
                
                let badge = fila.find('.span-diferencia-cierre');

                if (diferencia === 0) {
                    fila.removeClass('table-danger table-warning');
                    badge.removeClass('badge-danger badge-warning badge-info').addClass('badge-success').text('0');
                } else if (diferencia < 0) {
                    fila.removeClass('table-warning').addClass('table-danger');
                    badge.removeClass('badge-success badge-warning badge-info').addClass('badge-danger').text(diferencia);
                    novedades.push("FALTANTE EN CIERRE: " + nombreProducto + " (" + Math.abs(diferencia) + " und)");
                } else {
                    fila.removeClass('table-danger').addClass('table-warning');
                    badge.removeClass('badge-success badge-danger badge-warning').addClass('badge-info').text('+' + diferencia);
                    novedades.push("SOBRANTE EN CIERRE: " + nombreProducto + " (+" + diferencia + " und)");
                }
            });

            if (novedades.length > 0) {
                $('#reporte_descuadre_cierre').val("RESUMEN Y NOVEDADES DE CIERRE:\n- " + novedades.join("\n- "));
            } else {
                $('#reporte_descuadre_cierre').val("Cierre sin compras, pagos a trabajadora ni diferencias en dinero/inventario.");
            }
        }

        $(document).on('input change', '.input-conteo-cierre, #total_efectivo_real', function() {
            actualizarReporteCierre();
        });

        $(document).on('input change', '#pago_trabajadora', function() {
            recalcularDineroEsperado();
        });

        $('#btnGuardarVentaOlvidada').on('click', function() {
            let productoId = $('#select_producto').val();
            let cantidad = parseInt($('#input_cantidad').val()) || 0;
            let motivo = $('#input_motivo').val();

            if (!productoId || cantidad <= 0) {
                Swal.fire('Campos requeridos', 'Seleccione un producto y una cantidad válida.', 'warning');
                return;
            }

            let formData = $('#formVentaOlvidada').serialize();

            $.ajax({
                url: "{{ route('turnos.registrarVentaOlvidada') }}",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: formData,
                success: function(response) {
                    if (response.success) {
                        totalVentasEfectivoTurno += response.subtotal;
                        $('#texto_total_ventas').text('$' + totalVentasEfectivoTurno.toLocaleString('es-CO'));

                        let inputEsperado = $('#input_esperado_' + response.producto_id);
                        let badgeEsperado = $('#badge_esperado_' + response.producto_id);
                        let inputConteo = $('input[data-producto-id="' + response.producto_id + '"]');

                        if (inputEsperado.length) {
                            inputEsperado.val(response.nuevo_stock_esperado);
                            badgeEsperado.text(response.nuevo_stock_esperado);
                            inputConteo.attr('data-esperado', response.nuevo_stock_esperado);
                        }

                        let textoNovedad = response.cantidad + "x " + response.producto_nombre + " ($" + response.subtotal.toLocaleString('es-CO') + ")";
                        if (motivo) {
                            textoNovedad += " - Motivo: " + motivo;
                        }
                        ventasOlvidadas.push(textoNovedad);

                        $('#modalVentaOlvidada').modal('hide');
                        $('#formVentaOlvidada')[0].reset();

                        recalcularDineroEsperado();
                        Swal.fire('Venta Agregada', response.message, 'success');
                    } else {
                        Swal.fire('Error', response.message || 'No se pudo registrar la venta.', 'error');
                    }
                },
                error: function(xhr) {
                    let msg = 'Ocurrió un error al procesar la solicitud.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        recalcularDineroEsperado();
    });
</script>
@stop