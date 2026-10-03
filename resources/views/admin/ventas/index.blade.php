@extends('adminlte::page')

@section('title', 'Historial de Ventas')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0 text-dark font-weight-bold"><i class="fas fa-history text-primary mr-2"></i>Historial de Ventas</h1>
    <a href="{{ route('ventas.create') }}" class="btn btn-primary font-weight-bold">
        <i class="fas fa-plus-circle mr-1"></i> Nueva Venta (POS)
    </a>
</div>
@stop

@section('content')
<div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
        <h3 class="card-title font-weight-bold m-0">
            <i class="fas fa-list mr-2 text-primary"></i>Historial de Ventas
        </h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover text-center align-middle" id="tablaVentas">
                <thead class="thead-dark">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Fecha y Hora</th>
                        <th>Atendido Por</th>
                        <th>Cliente / Deudor / Remitente</th>
                        <th>Método Pago</th>
                        <th class="text-center">Estado Pago</th>
                        <th class="text-right">Total</th>
                        <th style="width: 190px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventas as $venta)
                        <tr>
                            <td class="text-center font-weight-bold">{{ $venta->id }}</td>
                            <td>{{ $venta->created_at->format('d/m/Y h:i A') }}</td>
                            <td>{{ $venta->user->name ?? 'N/A' }}</td>
                            
                            {{-- CLIENTE / DEUDOR / REMITENTE (Icono unificado) --}}
                            <td>
                                @if($venta->cliente_fiado)
                                    <span class="font-weight-bold text-dark text-capitalize">
                                        <i class="fas fa-user mr-1 text-secondary"></i> {{ $venta->cliente_fiado }}
                                    </span>
                                @else
                                    <span class="text-muted"><i class="fas fa-user mr-1 text-secondary"></i> Cliente Ocasional</span>
                                @endif
                            </td>

                            {{-- MÉTODO DE PAGO VALIDADO --}}
                            <td>
                                @if($venta->metodo_pago === 'transferencia')
                                    <span class="badge badge-primary px-2 py-1">
                                        <i class="fas fa-mobile-alt mr-1"></i> Transferencia
                                    </span>
                                @elseif($venta->metodo_pago === 'fiado' && $venta->estado_pago === 'pagado')
                                    <span class="badge badge-info px-2 py-1" title="Fiado ya saldado">
                                        <i class="fas fa-hand-holding-usd mr-1"></i> Fiado (Saldado)
                                    </span>
                                @elseif($venta->metodo_pago === 'fiado' && $venta->estado_pago === 'pendiente')
                                    <span class="badge badge-warning px-2 py-1">
                                        <i class="fas fa-clock mr-1"></i> Fiado (Pendiente)
                                    </span>
                                @elseif($venta->metodo_pago === 'efectivo')
                                    <span class="badge badge-success px-2 py-1">
                                        <i class="fas fa-money-bill-wave mr-1"></i> Efectivo
                                    </span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1 text-capitalize">{{ $venta->metodo_pago }}</span>
                                @endif
                            </td>
                            
                            {{-- ETIQUETA DE ESTADO Y BOTÓN DE PAGO FIADO --}}
                            <td class="text-center">
                                @if($venta->estado_pago === 'pendiente')
                                    <span class="badge badge-danger px-2 py-1 mb-1 d-block font-weight-bold">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Pendiente
                                    </span>
                                    <button type="button" 
                                            class="btn btn-xs btn-outline-success font-weight-bold btn-saldar-deuda" 
                                            data-id="{{ $venta->id }}" 
                                            data-cliente="{{ $venta->cliente_fiado }}" 
                                            data-total="${{ number_format($venta->total, 0, ',', '.') }}">
                                        <i class="fas fa-check mr-1"></i> Marcar como Pagado
                                    </button>
                                @else
                                    <span class="badge badge-success px-2 py-1 font-weight-bold">
                                        <i class="fas fa-check-circle mr-1"></i> Pagado
                                    </span>
                                @endif
                            </td>

                            {{-- TOTAL --}}
                            <td class="text-right font-weight-bold text-success">
                                ${{ number_format($venta->total, 0, ',', '.') }}
                            </td>

                            {{-- ACCIONES --}}
                            <td class="text-center align-middle">
                                <div style="display: flex; justify-content: center; align-items: center; gap: 6px;">
                                    <a href="{{ route('ventas.show', $venta->id) }}" 
                                       class="btn btn-info btn-sm font-weight-bold shadow-sm" 
                                       title="Ver Detalle">
                                        <i class="fas fa-eye mr-1"></i> Ver
                                    </a>
                                    <form action="{{ route('ventas.destroy', $venta->id) }}" method="POST" class="d-inline form-secured" data-secured-message="¿Eliminar venta? Stock será devuelto.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-danger btn-sm font-weight-bold shadow-sm text-white" 
                                                title="Eliminar Venta">
                                            <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL PARA SALDAR DEUDA DE FIADO --}}
<div class="modal fade" id="modalSaldarDeuda" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold" id="modalLabel"><i class="fas fa-hand-holding-usd mr-2"></i> Saldar Deuda de Fiado</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formSaldarDeuda" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p class="mb-2"><strong>Cliente:</strong> <span id="deudorNombre" class="text-primary font-weight-bold text-capitalize"></span></p>
                    <p class="mb-3"><strong>Monto a Saldar:</strong> <span id="deudorTotal" class="text-success font-weight-bold h5"></span></p>
                    
                    <div class="form-group">
                        <label for="metodo_pago_saldo" class="font-weight-bold">¿Cómo ingresa este dinero a la caja?</label>
                        <select name="metodo_pago_saldo" id="metodo_pago_saldo" class="form-control" required>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Registrar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@include('admin.partials._datatables', ['tableId' => 'tablaVentas', 'entidad' => 'Ventas', 'conBotones' => true])

@section('js')
@include('admin.partials.pin-security')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $(document).on('click', '.btn-saldar-deuda', function() {
            let id = $(this).data('id');
            let cliente = $(this).data('cliente');
            let total = $(this).data('total');

            $('#deudorNombre').text(cliente);
            $('#deudorTotal').text(total);
            
            let urlAction = "{{ url('/admin/ventas') }}/" + id + "/pagar-fiado";
            $('#formSaldarDeuda').attr('action', urlAction);

            $('#modalSaldarDeuda').modal('show');
        });
    });
</script>
@stop