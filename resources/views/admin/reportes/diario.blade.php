@extends('adminlte::page')

@section('title', 'Reporte Diario de Operaciones')

@section('css')
<style>
    /* Estilos corporativos mejorados */
    .dashboard-bg { background-color: #f4f6f9; }
    .kpi-card { border: none; border-radius: 10px; transition: transform 0.2s ease, box-shadow 0.2s ease; overflow: hidden; }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important; }
    .kpi-icon { font-size: 3rem; opacity: 0.15; position: absolute; right: 15px; bottom: 10px; }
    
    .card-custom { border-radius: 10px; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
    .card-header-custom { background-color: #fff; border-bottom: 1px solid #eaeaea; border-radius: 10px 10px 0 0 !important; padding: 1.2rem 1.5rem; }
    .section-title { font-size: 1.1rem; font-weight: 700; color: #343a40; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .table-modern { margin-bottom: 0; }
    .table-modern thead th { border-top: none; border-bottom: 2px solid #edf2f9; color: #495057; font-size: 0.8rem; text-transform: uppercase; font-weight: 700; }
    .table-modern tbody td { vertical-align: middle; border-bottom: 1px solid #edf2f9; color: #212529; font-size: 0.95rem; }
    
    .badge-soft-success { background-color: #d1f2e1; color: #0f5132; }
    .badge-soft-danger { background-color: #f8d7da; color: #842029; }
    .badge-soft-warning { background-color: #fff3cd; color: #664d03; }
    .badge-soft-info { background-color: #cff4fc; color: #055160; }
    
    .math-operator { font-size: 1.8rem; color: #6c757d; font-weight: bold; }
    .amount-display { font-family: 'Consolas', 'Courier New', monospace; font-size: 1.3rem; font-weight: bold; }
    .amount-total { font-size: 2.2rem; color: #198754; font-weight: 900; letter-spacing: -1px; }
    
    /* Etiquetas más oscuras para mejor lectura */
    .label-title { font-size: 0.85rem; letter-spacing: 0.5px; color: #495057; }
</style>
@stop

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
        <div>
            <h1 class="m-0 text-dark font-weight-bold" style="letter-spacing: -0.5px;">Dashboard de Cierre Diario</h1>
            <p class="text-secondary mb-0">Resumen operativo, financiero y de inventario</p>
        </div>
        <form method="GET" action="{{ route('reportes.diario') }}" class="d-flex align-items-center bg-white p-2 rounded shadow-sm border">
            <div class="input-group input-group-sm mr-3">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-calendar-alt text-dark"></i></span>
                </div>
                <input type="date" id="fecha" name="fecha" value="{{ $fecha }}" class="form-control border-0 font-weight-bold text-dark" style="background-color: #f8f9fa;" onchange="this.form.submit()">
            </div>
            <a href="{{ route('reportes.diario.pdf', ['fecha' => $fecha]) }}" class="btn btn-danger btn-sm font-weight-bold text-white px-3 shadow-sm" target="_blank">
                <i class="fas fa-file-pdf mr-1"></i> Exportar PDF
            </a>
        </form>
    </div>
@stop

@section('content')
<div class="container-fluid pb-5">

    {{-- 1. KPIs --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="card kpi-card bg-white shadow-sm h-100 border-left border-info" style="border-left-width: 4px !important;">
                <div class="card-body position-relative">
                    <h6 class="label-title font-weight-bold text-uppercase mb-1">Total Ventas (Bruto)</h6>
                    <h3 class="mb-0 font-weight-bold text-dark amount-display">${{ number_format($totalVendido, 0, ',', '.') }}</h3>
                    @if(($totalDescuentos ?? 0) > 0)
                        <span class="badge badge-warning text-dark font-weight-bold mt-1">
                            <i class="fas fa-tags mr-1"></i> Ahorro Promos: -${{ number_format($totalDescuentos, 0, ',', '.') }}
                        </span>
                    @endif
                    <i class="fas fa-chart-line kpi-icon text-info"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card kpi-card bg-white shadow-sm h-100 border-left border-success" style="border-left-width: 4px !important;">
                <div class="card-body position-relative">
                    <h6 class="label-title font-weight-bold text-uppercase mb-1">Efectivo Ingresado</h6>
                    <h3 class="mb-0 font-weight-bold text-success amount-display">${{ number_format($totalEfectivo, 0, ',', '.') }}</h3>
                    <i class="fas fa-money-bill-wave kpi-icon text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card kpi-card bg-white shadow-sm h-100 border-left border-primary" style="border-left-width: 4px !important;">
                <div class="card-body position-relative">
                    <h6 class="label-title font-weight-bold text-uppercase mb-1">Transferencias / Nequi</h6>
                    <h3 class="mb-0 font-weight-bold text-primary amount-display">${{ number_format($totalNequi, 0, ',', '.') }}</h3>
                    <i class="fas fa-mobile-alt kpi-icon text-primary"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="card kpi-card bg-white shadow-sm h-100 border-left border-danger" style="border-left-width: 4px !important;">
                <div class="card-body position-relative">
                    <h6 class="label-title font-weight-bold text-uppercase mb-1">Nuevas Deudas (Fiados)</h6>
                    <h3 class="mb-0 font-weight-bold text-danger amount-display">${{ number_format($totalFiadoNuevo, 0, ',', '.') }}</h3>
                    <i class="fas fa-file-invoice-dollar kpi-icon text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. LIQUIDACIÓN Y CUADRE DE CAJA (Estructura de grilla corregida) --}}
    <div class="card card-custom mt-2">
        <div class="card-header-custom d-flex align-items-center">
            <div class="bg-success text-white rounded p-2 mr-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                <i class="fas fa-cash-register"></i>
            </div>
            <h3 class="section-title">Liquidación Financiera (Caja Física)</h3>
        </div>
        <div class="card-body bg-white py-4 rounded-bottom">
            {{-- Usamos row y col para asegurar que no se estire excesivamente --}}
            <div class="row align-items-center justify-content-center text-center">
                
                <div class="col-md-2 col-sm-6 mb-3">
                    <p class="label-title text-uppercase font-weight-bold mb-1">Base Inicial</p>
                    <h4 class="amount-display text-dark mb-0">${{ number_format($baseInicial ?? 0, 0, ',', '.') }}</h4>
                </div>

                <div class="col-auto mb-3 d-none d-md-block"><span class="math-operator">-</span></div>

                <div class="col-md-2 col-sm-6 mb-3">
                    <p class="label-title text-uppercase font-weight-bold mb-1">Sueldo Cajero</p>
                    <h4 class="amount-display text-danger mb-0">-${{ number_format($sueldo, 0, ',', '.') }}</h4>
                </div>

                <div class="col-auto mb-3 d-none d-md-block"><span class="math-operator">-</span></div>

                <div class="col-md-2 col-sm-6 mb-3">
                    <p class="label-title text-uppercase font-weight-bold mb-1">Gastos / Compras</p>
                    <h4 class="amount-display text-danger mb-0">-${{ number_format($totalGastos, 0, ',', '.') }}</h4>
                </div>

                <div class="col-auto mb-3 d-none d-md-block"><span class="math-operator">-</span></div>

                <div class="col-md-2 col-sm-6 mb-3">
                    <p class="label-title text-uppercase font-weight-bold mb-1">Fondo Sig. Turno</p>
                    <h4 class="amount-display text-warning mb-0">-${{ number_format($baseSiguiente, 0, ',', '.') }}</h4>
                </div>

                <div class="col-auto mb-3 d-none d-lg-block"><span class="math-operator text-dark">=</span></div>

                <div class="col-lg-3 col-md-6 col-sm-12 mt-3 mt-lg-0">
                    <div class="px-4 py-3 rounded shadow-sm" style="background-color: #f0fdf4; border: 2px solid #28a745;">
                        <p class="text-success text-uppercase font-weight-bold mb-1" style="font-size: 0.9rem;">Efectivo a Entregar</p>
                        <h1 class="amount-display amount-total mb-0">${{ number_format($dineroEntregado, 0, ',', '.') }}</h1>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- 3. AUDITORÍA DE INVENTARIO --}}
    <div class="card card-custom">
        <div class="card-header-custom d-flex align-items-center">
            <div class="bg-dark text-white rounded p-2 mr-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                <i class="fas fa-boxes"></i>
            </div>
            <h3 class="section-title">Auditoría de Inventario</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-modern text-center">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-left pl-4">Producto</th>
                            <th>Stock Teórico (Sistema)</th>
                            <th>Stock Físico (Declarado)</th>
                            <th>Diferencia</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detallesTurno as $item)
                            @php
                                $fisico = $item->stock_fisico_cierre !== null ? $item->stock_fisico_cierre : $item->stock_esperado;
                                $diferencia = $fisico - ($item->stock_esperado ?? 0);
                            @endphp
                            <tr>
                                <td class="text-left pl-4 font-weight-bold">{{ $item->producto->nombre ?? 'Producto' }}</td>
                                <td><span class="text-muted">{{ $item->stock_esperado ?? 0 }}</span></td>
                                <td><span class="badge badge-secondary px-3 py-2" style="font-size: 0.9rem;">{{ $fisico }}</span></td>
                                <td>
                                    <span class="amount-display {{ $diferencia < 0 ? 'text-danger' : ($diferencia > 0 ? 'text-warning' : 'text-success') }}">
                                        {{ $diferencia > 0 ? '+' : '' }}{{ $diferencia }}
                                    </span>
                                </td>
                                <td>
                                    @if($diferencia < 0)
                                        <span class="badge badge-soft-danger px-3 py-2"><i class="fas fa-exclamation-circle mr-1"></i> Faltante</span>
                                    @elseif($diferencia > 0)
                                        <span class="badge badge-soft-warning px-3 py-2"><i class="fas fa-exclamation-triangle mr-1"></i> Sobrante</span>
                                    @else
                                        <span class="badge badge-soft-success px-3 py-2"><i class="fas fa-check-circle mr-1"></i> Cuadre Exacto</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted py-5"><i class="fas fa-inbox fa-2x mb-2 text-light"></i><br>No hay registros de inventario para este turno.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 4. DESGLOSE DINÁMICO DE VENTAS POR CATEGORÍA --}}
    <div class="row">
        @foreach($categorias as $categoria)
            <div class="col-lg-6 col-12 mb-4">
                <div class="card card-custom h-100 shadow-sm border-0">
                    <div class="card-header-custom d-flex justify-content-between align-items-center bg-white border-bottom">
                        <div class="d-flex align-items-center">
                            <span class="bg-primary text-white rounded p-1 mr-2 d-inline-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                <i class="fas fa-tags" style="font-size: 0.9rem;"></i>
                            </span>
                            <h3 class="section-title text-dark mb-0 font-weight-bold" style="font-size: 0.95rem;">
                                VENTA DE {{ strtoupper($categoria->nombre) }}
                            </h3>
                        </div>
                        <div class="text-right">
                            @if(($categoria->total_descuento ?? 0) > 0)
                                <span class="badge badge-warning text-dark font-weight-bold mr-1" title="Ahorro en Promociones">
                                    <i class="fas fa-percent mr-1"></i> Ahorro: -${{ number_format($categoria->total_descuento, 0, ',', '.') }}
                                </span>
                            @endif
                            <span class="badge badge-soft-success font-weight-bold px-2 py-1" style="font-size: 0.95rem;">
                                Total: ${{ number_format($categoria->total_vendido ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-modern text-center mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-left pl-3" style="width: 28%;">Producto</th>
                                        <th style="width: 10%;">Cant.</th>
                                        <th style="width: 15%;">Precio Base</th>
                                        <th style="width: 18%;">Promoción</th>
                                        <th style="width: 14%;">Método</th>
                                        <th class="text-right pr-3" style="width: 15%;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categoria->ventas as $item)
                                        <tr>
                                            <td class="text-left pl-3 font-weight-bold text-dark">
                                                {{ $item->producto_nombre }}
                                            </td>
                                            <td>
                                                <span class="badge badge-soft-info px-2 py-1 font-weight-bold">{{ $item->cantidad }}</span>
                                            </td>
                                            <td class="text-muted">
                                                ${{ number_format($item->precio_unitario, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                @if($item->descuento > 0)
                                                    <span class="badge badge-warning font-weight-bold px-2 py-1 text-dark">
                                                        <i class="fas fa-tag mr-1"></i>Promo (-${{ number_format($item->descuento, 0, ',', '.') }})
                                                    </span>
                                                @elseif($item->tiene_promo)
                                                    <span class="badge badge-warning font-weight-bold px-2 py-1 text-dark">
                                                        <i class="fas fa-tag mr-1"></i>Promo
                                                    </span>
                                                @else
                                                    <span class="badge badge-light border text-muted px-2 py-1">Normal</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->metodo_pago === 'efectivo')
                                                    <span class="badge badge-soft-success px-2 py-1 font-weight-bold text-success border border-success">
                                                        <i class="fas fa-money-bill-wave mr-1"></i>Efectivo
                                                    </span>
                                                @elseif(in_array($item->metodo_pago, ['transferencia', 'nequi']))
                                                    <span class="badge badge-soft-info px-2 py-1 font-weight-bold text-primary border border-primary">
                                                        <i class="fas fa-mobile-alt mr-1"></i>Transf.
                                                    </span>
                                                @elseif($item->metodo_pago === 'fiado')
                                                    <span class="badge badge-soft-danger px-2 py-1 font-weight-bold text-danger border border-danger">
                                                        <i class="fas fa-user-clock mr-1"></i>Fiado
                                                    </span>
                                                @else
                                                    <span class="badge badge-light border px-2 py-1 text-dark font-weight-bold">
                                                        {{ ucfirst($item->metodo_pago) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-right pr-3 font-weight-bold text-success amount-display" style="font-size: 1rem;">
                                                ${{ number_format($item->subtotal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-muted py-4">
                                                <i class="fas fa-box-open mr-1 text-secondary"></i> Sin ventas registradas en este turno.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- CONTROL DE FIADOS (Nuevos y Abonos) --}}
    <div class="row">
        {{-- Fiados Nuevos --}}
        <div class="col-md-6 mb-4">
            <div class="card card-custom h-100 shadow-sm border-0">
                <div class="card-header-custom d-flex justify-content-between align-items-center bg-white border-bottom">
                    <div class="d-flex align-items-center">
                        <span class="bg-danger text-white rounded p-1 mr-2 d-inline-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                            <i class="fas fa-user-tag" style="font-size: 0.9rem;"></i>
                        </span>
                        <h3 class="section-title text-danger mb-0" style="font-size: 0.95rem;">Fiados Nuevos (Otorgados en el Turno)</h3>
                    </div>
                    <span class="badge badge-soft-danger font-weight-bold px-2 py-1">Total: ${{ number_format($totalFiadoNuevo, 0, ',', '.') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-modern text-center mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-left pl-4">Cliente / Deudor</th>
                                    <th class="text-right pr-4">Deuda Generada</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($fiadosDelDia as $fiado)
                                    <tr>
                                        <td class="text-left pl-4 font-weight-bold text-dark">{{ $fiado->cliente_fiado ?? 'Sin Nombre' }}</td>
                                        <td class="text-right pr-4 text-danger font-weight-bold amount-display" style="font-size: 1.05rem;">
                                            ${{ number_format($fiado->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-muted py-4"><i class="fas fa-check-circle text-success mr-1"></i> No se otorgaron fiados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Abonos Fiados --}}
        <div class="col-md-6 mb-4">
            <div class="card card-custom h-100 shadow-sm border-0">
                <div class="card-header-custom d-flex justify-content-between align-items-center bg-white border-bottom">
                    <div class="d-flex align-items-center">
                        <span class="bg-success text-white rounded p-1 mr-2 d-inline-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                            <i class="fas fa-hand-holding-usd" style="font-size: 0.9rem;"></i>
                        </span>
                        <h3 class="section-title text-success mb-0" style="font-size: 0.95rem;">Abonos a Fiados (Ingreso a Caja)</h3>
                    </div>
                    <span class="badge badge-soft-success font-weight-bold px-2 py-1">Total: ${{ number_format($totalCobradoFiados, 0, ',', '.') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-modern text-center mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-left pl-4">Cliente</th>
                                    <th>Medio</th>
                                    <th class="text-right pr-4">Monto Recibido</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cobrosFiadosDia as $cobro)
                                    <tr>
                                        <td class="text-left pl-4 font-weight-bold text-dark">{{ $cobro->cliente_fiado ?? 'Cliente' }}</td>
                                        <td>
                                            @if($cobro->metodo_pago_saldo === 'transferencia' || in_array($cobro->metodo_pago, ['transferencia', 'nequi']))
                                                <span class="badge badge-soft-info text-primary border border-primary px-2 py-1 font-weight-bold">Transf.</span>
                                            @else
                                                <span class="badge badge-soft-success text-success border border-success px-2 py-1 font-weight-bold">Efectivo</span>
                                            @endif
                                        </td>
                                        <td class="text-right pr-4 text-success font-weight-bold amount-display" style="font-size: 1.05rem;">
                                            ${{ number_format($cobro->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted py-4"><i class="fas fa-info-circle text-muted mr-1"></i> No se cobraron fiados en este turno.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. OBSERVACIONES DEL TURNO --}}
    @if(!empty($observaciones))
    <div class="card card-custom mt-4 mb-5" style="border-left: 5px solid #6c757d; background-color: #fdfdfd;">
        <div class="card-body">
            <h5 class="font-weight-bold text-dark mb-3" style="font-size: 1.1rem;">
                <i class="fas fa-comment-dots text-secondary mr-2"></i> Observaciones del Turno / Cierre
            </h5>
            <p class="mb-0 text-dark" style="white-space: pre-line; line-height: 1.6; font-size: 1rem;">
                {{ $observaciones }}
            </p>
        </div>
    </div>
    @endif

</div>
@stop