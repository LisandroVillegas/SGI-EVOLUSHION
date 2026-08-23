@extends('adminlte::page')

@section('title', 'Detalle de Compra')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Detalle de Compra #{{ $compra->id }}</h1>
        <a href="{{ route('compras.index') }}" class="btn btn-secondary">Volver al Historial</a>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <strong>Comprobante:</strong> {{ $compra->comprobante ?? 'N/A' }}
            </div>
            <div class="col-md-4">
                <strong>Fecha de Ingreso:</strong> {{ $compra->fecha }}
            </div>
            <div class="col-md-4">
                <strong>Total:</strong> ${{ number_format($compra->total, 2) }}
            </div>
        </div>

        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Producto</th>
                    <th>Cantidad Ingresada</th>
                    <th>Precio de Compra (Unitario)</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($compra->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>${{ number_format($detalle->precio_compra, 2) }}</td>
                        <td>${{ number_format($detalle->cantidad * $detalle->precio_compra, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop