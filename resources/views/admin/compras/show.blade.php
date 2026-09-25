@extends('adminlte::page')

@section('title', 'Detalle de Compra')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Detalle de Compra #{{ $compra->id }}</h1>
        <a href="{{ route('compras.index') }}" class="btn btn-secondary">Volver al Historial</a>
    </div>
@stop

@section('content')
<div class="card card-outline {{ ($compra->tipo ?? 'normal') === 'rapida' ? 'card-warning' : 'card-info' }}">
    <div class="card-header">
        <h3 class="card-title font-weight-bold">
            @if(($compra->tipo ?? 'normal') === 'rapida')
                <i class="fas fa-bolt text-warning mr-1"></i> Compra Rápida / Egreso Operativo
            @else
                <i class="fas fa-boxes text-info mr-1"></i> Compra de Productos para Inventario
            @endif
        </h3>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <strong>Comprobante:</strong> {{ $compra->comprobante ?? 'N/A' }}
            </div>
            <div class="col-md-3">
                <strong>Tipo de Registro:</strong> 
                @if(($compra->tipo ?? 'normal') === 'rapida')
                    <span class="badge badge-warning">Egreso Operativo (Rápido)</span>
                @else
                    <span class="badge badge-info">Ingreso de Inventario</span>
                @endif
            </div>
            <div class="col-md-3">
                <strong>Fecha de Ingreso:</strong> {{ $compra->fecha }}
            </div>
            <div class="col-md-3">
                <strong>Total Gastado:</strong> <span class="text-success font-weight-bold">${{ number_format($compra->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <hr>

        @if(($compra->tipo ?? 'normal') === 'rapida')
            <!-- Vista para Compra Rápida / Egreso -->
            <div class="callout callout-warning bg-light p-3 rounded">
                <h5><i class="fas fa-info-circle text-warning mr-1"></i> <strong>Concepto / Detalle del Gasto:</strong></h5>
                <p class="lead mb-0 font-weight-bold text-dark">{{ $compra->concepto ?? 'Sin concepto registrado' }}</p>
            </div>
            <small class="text-muted"><i class="fas fa-exclamation-circle mr-1"></i> Este registro corresponde a una Salida Operativa de Caja. No afectó el stock de ningún producto en el inventario.</small>
        @else
            <!-- Vista para Compra Normal (Productos) -->
            <h5 class="font-weight-bold mb-3"><i class="fas fa-list mr-1"></i> Desglose de Productos Ingresados</h5>
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cantidad Ingresada</th>
                        <th class="text-right">Precio de Compra (Unitario)</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compra->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nombre ?? 'Producto Eliminado' }}</td>
                            <td class="text-center">{{ $detalle->cantidad }}</td>
                            <td class="text-right">${{ number_format($detalle->precio_compra, 0, ',', '.') }}</td>
                            <td class="text-right font-weight-bold">${{ number_format($detalle->cantidad * $detalle->precio_compra, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No hay detalles registrados para esta compra.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>
</div>
@stop