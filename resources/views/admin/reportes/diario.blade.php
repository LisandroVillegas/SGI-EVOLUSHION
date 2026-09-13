@extends('adminlte::page')

@section('title', 'Reporte Diario de Ventas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-chart-line text-primary"></i> Reporte Diario de Ventas</h1>
        <form method="GET" action="{{ route('reportes.diario') }}" class="form-inline">
            <label for="fecha" class="mr-2 font-weight-bold">Fecha:</label>
            <input type="date" id="fecha" name="fecha" value="{{ $fecha }}" class="form-control mr-2" onchange="this.form.submit()">
            <a href="{{ route('reportes.diario.pdf', ['fecha' => $fecha]) }}" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
        </form>
    </div>
@stop

@section('content')
<div class="container-fluid">

    {{-- 1. Tarjetas de Resumen General --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>${{ number_format($totalVendido, 0, ',', '.') }}</h3>
                    <p>Total Vendido</p>
                </div>
                <div class="icon"><i class="fas fa-dollar-sign"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>${{ number_format($totalEfectivo, 0, ',', '.') }}</h3>
                    <p>Efectivo Recibido</p>
                </div>
                <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box" style="background-color: #6f42c1 !important; color: white;">
                <div class="inner">
                    <h3>${{ number_format($totalNequi, 0, ',', '.') }}</h3>
                    <p>Transferencias / Nequi</p>
                </div>
                <div class="icon"><i class="fas fa-mobile-alt"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>${{ number_format($totalFiadoNuevo, 0, ',', '.') }}</h3>
                    <p>Fiados Nuevos del Día</p>
                </div>
                <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
            </div>
        </div>
    </div>

    {{-- 1.1 Secciones de Control de Fiados (Nuevos y Cobrados) --}}
    <div class="row">
        {{-- Fiados Nuevos del Día (Pendientes) --}}
        <div class="col-md-6">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-clock"></i> Detalle de Fiados Nuevos (Pendientes)</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped text-center m-0">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Productos / Detalle</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fiadosDelDia as $fiado)
                                <tr>
                                    <td><strong>{{ $fiado->cliente_fiado ?? 'Sin Nombre' }}</strong></td>
                                    <td>
                                        @foreach($fiado->detalles as $det)
                                            <span class="badge badge-secondary">{{ $det->cantidad }}x {{ $det->producto->nombre ?? 'Art.' }}</span>
                                        @endforeach
                                    </td>
                                    <td><span class="text-danger font-weight-bold">${{ number_format($fiado->total, 0, ',', '.') }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted py-3">No hay cuentas fiadas registradas hoy.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Fiados Cobrados / Saldados Hoy --}}
        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-check-circle"></i> Fiados Cobrados Hoy (Ingresados a Caja)</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped text-center m-0">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Método de Pago</th>
                                <th>Monto Abonado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cobrosFiadosDia as $cobro)
                                <tr>
                                    <td><strong>{{ $cobro->cliente_fiado ?? 'Cliente' }}</strong></td>
                                    <td><span class="badge badge-info text-uppercase">{{ $cobro->metodo_pago }}</span></td>
                                    <td><span class="text-success font-weight-bold">${{ number_format($cobro->total, 0, ',', '.') }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted py-3">No se han registrado cobros de fiados en esta fecha.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Cócteles por Precio y Productos de Nevera --}}
    <div class="row">
        {{-- Cócteles por Rango de Precio --}}
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cocktail"></i> Venta de Cócteles </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped text-center m-0">
                        <thead>
                            <tr>
                                <th>Precio Unitario</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coctelesPorPrecio as $item)
                                <tr>
                                    <td>${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                                    <td><span class="badge badge-primary">{{ $item->total_cantidad }}</span></td>
                                    <td>${{ number_format($item->total_monto, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted py-3">No hay registros de cócteles hoy.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Ventas de Nevera --}}
        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-wine-bottle"></i> Nevera</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped text-center m-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventasNevera as $item)
                                <tr>
                                    <td>{{ $item->producto->nombre ?? 'Desconocido' }}</td>
                                    <td><span class="badge badge-success">{{ $item->cantidad }}</span></td>
                                    <td>${{ number_format($item->total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted py-3">No se vendió mercancía de nevera.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Control de Stock del Turno (Stock Esperado) --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-warehouse text-warning"></i> Control de Stock del Turno (Stock Esperado)</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped text-center m-0">
                        <thead>
                            <tr>
                                <th>PRODUCTO</th>
                                <th>STOCK FINAL / CANTIDAD</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($detallesTurno as $item)
                                <tr>
                                    <td><strong>{{ $item->producto->nombre ?? 'Producto' }}</strong></td>
                                    <td>
                                        <span class="badge bg-dark px-3 py-2" style="font-size: 14px;">
                                            {{ $item->stock_esperado ?? 0 }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-muted py-3">
                                        No hay un turno registrado o activo para esta fecha.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. Balance de Cierre de Caja del Turno --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cash-register"></i> Liquidación y Cuadre de Caja (Turno Actual)</h3>
                </div>
                <div class="card-body">
                    <div class="row text-center align-items-center">
                        <div class="col-md-2 border-right">
                            <span class="text-muted">Base Inicial</span><br>
                            <h5 class="font-weight-bold">${{ number_format($turno->base_caja ?? 0, 0, ',', '.') }}</h5>
                        </div>
                        <div class="col-md-2 border-right">
                            <span class="text-muted">Sueldo Empleado</span><br>
                            <h5 class="font-weight-bold text-danger">-${{ number_format($sueldo, 0, ',', '.') }}</h5>
                        </div>
                        <div class="col-md-2 border-right">
                            <span class="text-muted">Gastos del Día</span><br>
                            <h5 class="font-weight-bold text-danger">-${{ number_format($totalGastos, 0, ',', '.') }}</h5>
                        </div>
                        <div class="col-md-2 border-right">
                            <span class="text-muted">Base Siguiente Día</span><br>
                            <h5 class="font-weight-bold text-danger">-${{ number_format($baseSiguiente, 0, ',', '.') }}</h5>
                        </div>
                        <div class="col-md-4 bg-light py-3 rounded">
                            <span class="text-success font-weight-bold">DINERO NETO A ENTREGAR:</span>
                            <h2 class="font-weight-bold text-success mb-0">${{ number_format($dineroEntregado, 0, ',', '.') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@stop