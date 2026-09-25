@extends('adminlte::page')

@section('title', 'Detalle de Venta #' . $venta->id)

@section('content_header')
<nav aria-label="breadcrumb" style="font-size: 16pt">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Inicio</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/ventas') }}">Historial de Ventas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Venta #{{ $venta->id }}</li>
  </ol>
</nav>
<hr class="mt-0">
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-warning shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold m-0">
                    <i class="fas fa-receipt text-warning mr-1"></i> Comprobante de Venta #{{ sprintf('%06d', $venta->id) }}
                </h3>
                <div class="card-tools">
                    <a href="{{ url('/admin/ventas') }}" class="btn btn-sm btn-secondary font-weight-bold">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                    <button type="button" onclick="window.print();" class="btn btn-sm btn-outline-dark font-weight-bold ml-1">
                        <i class="fas fa-print mr-1"></i> Imprimir
                    </button>
                </div>
            </div>

            <div class="card-body">
                {{-- CABECERA CON INFORMACIÓN GENERAL, FIADOS Y PROMOCIÓN --}}
                <div class="row bg-light p-3 rounded border mb-4">
                    <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                        <small class="text-muted d-block font-weight-bold text-uppercase">Fecha y Hora</small>
                        <span class="text-dark font-weight-bold">{{ $venta->created_at->format('d/m/Y h:i A') }}</span>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                        <small class="text-muted d-block font-weight-bold text-uppercase">Atendido por (Cajero)</small>
                        <span class="text-dark font-weight-bold">{{ $venta->user->name ?? 'Usuario no registrado' }}</span>
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                        <small class="text-muted d-block font-weight-bold text-uppercase">Cliente / Estado</small>
                        @if($venta->metodo_pago === 'transferencia' && $venta->cliente_fiado)
                            <span class="text-dark font-weight-bold text-capitalize d-block mb-1">
                                <i class="fas fa-user-circle text-primary mr-1"></i> {{ $venta->cliente_fiado }}
                            </span>
                            <span class="badge badge-primary px-2 py-1"><i class="fas fa-mobile-alt mr-1"></i> Transf. Recibida</span>
                        @elseif($venta->metodo_pago === 'fiado' && $venta->cliente_fiado)
                            <span class="text-dark font-weight-bold text-capitalize d-block mb-1">
                                <i class="fas fa-user text-warning mr-1"></i> {{ $venta->cliente_fiado }}
                            </span>
                            @if($venta->estado_pago === 'pendiente')
                                <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> Deuda Pendiente</span>
                            @else
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Cuenta Saldada</span>
                            @endif
                        @else
                            <span class="text-muted font-weight-bold">Cliente General</span>
                        @endif
                    </div>
                    <div class="col-md-2 col-sm-6 mb-2 mb-md-0">
                        <small class="text-muted d-block font-weight-bold text-uppercase">Método de Pago</small>
                        @php
                            $metodo = strtolower($venta->metodo_pago ?? 'efectivo');
                        @endphp
                        @if($metodo === 'efectivo')
                            <span class="badge badge-success px-2 py-1" style="font-size: 0.85rem;">
                                <i class="fas fa-money-bill-wave mr-1"></i> Efectivo
                            </span>
                        @elseif($metodo === 'transferencia' || $metodo === 'nequi')
                            <span class="badge badge-primary px-2 py-1" style="font-size: 0.85rem;">
                                <i class="fas fa-mobile-alt mr-1"></i> Nequi / Transf.
                            </span>
                        @elseif($metodo === 'mixto')
                            <span class="badge badge-info px-2 py-1" style="font-size: 0.85rem;">
                                <i class="fas fa-coins mr-1"></i> Pago Mixto
                            </span>
                        @elseif($metodo === 'fiado')
                            <span class="badge badge-warning px-2 py-1" style="font-size: 0.85rem;">
                                <i class="fas fa-hand-holding-usd mr-1"></i> Fiado
                            </span>
                        @else
                            <span class="badge badge-secondary px-2 py-1" style="font-size: 0.85rem;">
                                {{ strtoupper($metodo) }}
                            </span>
                        @endif
                    </div>
                    <div class="col-md-1 col-sm-6 mb-2 mb-md-0">
                        <small class="text-muted d-block font-weight-bold text-uppercase">Promoción</small>
                        @if($venta->aplica_promocion)
                            <span class="badge badge-warning px-2 py-1 font-weight-bold text-dark" style="font-size: 0.85rem;">
                                <i class="fas fa-cocktail mr-1"></i> Promo
                            </span>
                        @else
                            <span class="badge badge-secondary px-2 py-1" style="font-size: 0.85rem;">
                                Sin Promo
                            </span>
                        @endif
                    </div>
                    <div class="col-md-2 col-sm-6 text-md-right">
                        <small class="text-muted d-block font-weight-bold text-uppercase">Monto Total</small>
                        <h4 class="text-success font-weight-bold mb-0">${{ number_format($venta->total, 0, ',', '.') }}</h4>
                    </div>
                </div>

                {{-- TABLA DE DETALLES DE PRODUCTOS --}}
                <h5 class="font-weight-bold mb-3"><i class="fas fa-cocktail text-warning mr-1"></i> Productos Vendidos</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle">
                        <thead class="bg-light text-uppercase small font-weight-bold">
                            <tr>
                                <th style="width: 60px;" class="text-center">#</th>
                                <th>Producto</th>
                                <th class="text-center" style="width: 140px;">Precio Unitario</th>
                                <th class="text-center" style="width: 120px;">Cantidad</th>
                                <th class="text-right" style="width: 160px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($venta->detalles as $loop_index => $detalle)
                                @php
                                    $precio = $detalle->precio_unitario ?? $detalle->precio ?? 0;
                                    $subtotal = $detalle->subtotal ?? ($precio * $detalle->cantidad);
                                @endphp
                                <tr>
                                    <td class="text-center align-middle font-weight-bold text-muted">{{ $loop_index + 1 }}</td>
                                    <td class="align-middle">
                                        <strong class="text-dark">{{ $detalle->producto->nombre ?? 'Producto no encontrado / Eliminado' }}</strong>
                                    </td>
                                    <td class="text-center align-middle">${{ number_format($precio, 0, ',', '.') }}</td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-secondary px-2 py-1" style="font-size: 0.9rem;">
                                            {{ $detalle->cantidad }}
                                        </span>
                                    </td>
                                    <td class="text-right align-middle font-weight-bold text-dark">
                                        ${{ number_format($subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <em>No hay detalles registrados para esta venta.</em>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="4" class="text-right font-weight-bold text-uppercase">Total General:</td>
                                <td class="text-right font-weight-bold text-success" style="font-size: 1.2rem;">
                                    ${{ number_format($venta->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if(!empty($venta->observaciones))
                    <div class="mt-4 p-3 bg-light rounded border">
                        <strong class="text-dark d-block mb-1"><i class="fas fa-sticky-note text-warning mr-1"></i> Observaciones:</strong>
                        <p class="text-muted m-0">{{ $venta->observaciones }}</p>
                    </div>
                @endif
            </div>

            <div class="card-footer bg-light text-right py-3">
                <a href="{{ url('/admin/ventas') }}" class="btn btn-secondary px-4 font-weight-bold">
                    <i class="fas fa-arrow-left mr-1"></i> Regresar al Historial
                </a>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    @media print {
        .main-sidebar, .main-header, .card-tools, .card-footer, .breadcrumb {
            display: none !important;
        }
        .content-wrapper {
            margin-left: 0 !important;
            background-color: #fff !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@stop