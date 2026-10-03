@extends('adminlte::page')

@section('title', 'Reporte Diario de Operaciones')

@section('css')
<style>
    /* Estilos SaaS Moderno */
    .dashboard-bg { background-color: #F8FAFC; }
    .kpi-card { background-color: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; transition: transform 0.2s ease, box-shadow 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden; }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06) !important; }
    .kpi-icon { font-size: 3rem; opacity: 0.2; position: absolute; right: 15px; bottom: -5px; }
    
    /* Estilos de jerarquía y relieve encapsulados para este reporte */
    .card { 
        border: 1.5px solid #0F172A !important; 
        border-radius: 12px !important; 
        overflow: hidden !important; 
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.16), 0 2px 6px rgba(0, 0, 0, 0.1) !important; 
    }
    .card-header {
        border-bottom: none !important;
    }

    /* Tarjeta Contenedora Principal */
    .card-custom { 
        background-color: #FFFFFF; 
        border-radius: 12px !important; 
        border: 1.5px solid #0F172A !important; 
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.16), 0 2px 6px rgba(0, 0, 0, 0.1) !important; 
        margin-bottom: 1.5rem; 
        overflow: hidden !important; 
    }

    /* Encabezado Principal del Bloque (Negro) */
    .card-header-custom { 
        background-color: #0F172A !important; 
        border-bottom: 1px solid #1E293B !important; 
        padding: 1rem 1.25rem; 
        color: #FFFFFF; 
    }
    .section-title { 
        font-size: 0.95rem; 
        font-weight: 700; 
        color: #FFFFFF !important; 
        margin: 0; 
        text-transform: uppercase; 
        letter-spacing: 0.05em; 
    }
    
    /* Encabezados de Columnas de la Tabla */
    .table-modern { margin-bottom: 0; }
    .table-modern thead th, .thead-light th { 
        border-top: none !important; 
        border-bottom: 1px solid #E2E8F0 !important; 
        background-color: #F1F5F9 !important; 
        color: #334155 !important; 
        font-size: 0.8rem; 
        text-transform: uppercase; 
        font-weight: 700; 
        letter-spacing: 0.05em; 
        padding: 12px 15px; 
    }
    .table-modern tbody td { vertical-align: middle; border-bottom: 1px solid #E2E8F0; color: #0F172A; font-size: 0.95rem; font-weight: 600; }
    
    .badge-saas-success { background-color: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
    .badge-saas-danger { background-color: #FFF1F2; color: #E11D48; border: 1px solid #FECDD3; }
    .badge-saas-warning { background-color: #FEF08A !important; color: #713F12 !important; border: 1px solid #FDE047 !important; font-weight: 700 !important; }
    .badge-saas-info { background-color: #F0F9FF; color: #0369A1; border: 1px solid #BAE6FD; }
    .badge-soft-info { background-color: #E0F2FE; color: #0369A1; border: 1px solid #BAE6FD; }
    
    .math-operator { font-size: 1.5rem; color: #94A3B8; font-weight: 300; }
    .amount-display { font-variant-numeric: tabular-nums; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; font-weight: 700; }
    
    /* Hero Card */
    .label-title { font-size: 0.75rem; letter-spacing: 0.05em; color: #9eb0c9ff; text-transform: uppercase; font-weight: 600; margin-bottom: 0.5rem; }
    .kpi-amount { font-size: 1.75rem; color: #0F172A; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 0; }
    
    /* Botón SaaS Exportar PDF */
    .btn-saas-pdf { background-color: #ffffffff; color: #fb5252ff; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; padding: 0.6rem 1.2rem; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s ease; white-space: nowrap; box-shadow: 0 2px 4px rgba(255, 20, 71, 0.87); }
    .btn-saas-pdf:hover { background-color: #ff8888ff; color: #000000ff; text-decoration: none; transform: translateY(-1px); box-shadow: 0 5px 10px rgba(0, 0, 0, 0.35); }
    .btn-saas-pdf i { font-size: 1rem; }
    
    /* Vibrant KPI Cards */
    .kpi-card-vibrant { border-radius: 12px; border: none; overflow: hidden; color: #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s ease, box-shadow 0.2s ease; position: relative; padding: 1.25rem; }
    .kpi-card-vibrant:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.15); }
    .kpi-bg-ventas { background: linear-gradient(135deg, #10B981, #059669); }
    .kpi-bg-efectivo { background: linear-gradient(135deg, #059669, #047857); }
    .kpi-bg-nequi { background: linear-gradient(135deg, #3B82F6, #1D4ED8); }
    .kpi-bg-fiados { background: linear-gradient(135deg, #F59E0B, #D97706); }
    
    .kpi-vibrant-title { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; opacity: 0.9; margin-bottom: 0.25rem; }
    .kpi-vibrant-amount { font-size: 1.8rem; font-weight: 800; line-height: 1.2; font-variant-numeric: tabular-nums; margin-bottom: 0; }
    .kpi-vibrant-icon { font-size: 3.5rem; position: absolute; right: -5px; bottom: -10px; opacity: 0.2; }
    .badge-vibrant { background-color: rgba(255, 255, 255, 0.2); color: #fff; border: 1px solid rgba(255, 255, 255, 0.3); padding: 4px 8px; border-radius: 6px; font-weight: 600; font-size: 0.75rem; display: inline-block; margin-top: 8px; }

    /* Dark Mode Premium Card */
    .card-dark-premium { background-color: #0F172A; border-radius: 12px; border: 1px solid #1E293B; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3); color: #FFFFFF; margin-bottom: 1.5rem; }
    .card-header-dark { background-color: transparent; border-bottom: 1px solid #1E293B; padding: 1.25rem 1.5rem; border-radius: 12px 12px 0 0; display: flex; align-items: center; }
    .dark-section-title { font-size: 1.1rem; font-weight: 800; color: #F8FAFC; margin: 0; text-transform: uppercase; letter-spacing: 0.05em; }
    
    .dark-math-operator { font-size: 1.8rem; color: #475569; font-weight: 400; }
    .dark-label-title { font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: #94A3B8; margin-bottom: 0.5rem; letter-spacing: 0.05em; }
    .dark-amount-display { font-size: 1.3rem; font-weight: 800; font-variant-numeric: tabular-nums; }
    
    /* Colores del modo oscuro */
    .dark-text-base { color: #38BDF8 !important; }
    .dark-text-success { color: #34D399 !important; }
    .dark-text-danger { color: #F87171 !important; }
    .dark-text-warning { color: #FBBF24 !important; }

    /* Efectivo a Entregar Glowing */
    .glowing-box { background-color: rgba(16, 185, 129, 0.1); border: 1px solid #10B981; border-radius: 10px; box-shadow: 0 0 15px rgba(16, 185, 129, 0.2); padding: 1rem 1.25rem; text-align: center; }
    .glowing-label { font-size: 0.85rem; text-transform: uppercase; font-weight: 800; color: #10B981; margin-bottom: 0.25rem; letter-spacing: 0.05em; }
    .glowing-amount { font-size: 2rem; font-weight: 900; color: #34D399; font-variant-numeric: tabular-nums; line-height: 1.1; margin-bottom: 0; }
</style>
@stop

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
        <div class="d-flex align-items-center">
            <div class="bg-dark text-white rounded p-2 mr-3 shadow-sm d-flex justify-content-center align-items-center" style="width: 45px; height: 45px;">
                <i class="fas fa-chart-pie" style="font-size: 1.4rem;"></i>
            </div>
            <div>
                <h1 class="m-0 text-dark font-weight-bold" style="letter-spacing: -0.5px;">Dashboard de Cierre Diario</h1>
                <p class="text-secondary mb-0">Resumen operativo, financiero y de inventario</p>
            </div>
        </div>
        <form method="GET" action="{{ route('reportes.diario') }}" class="d-flex align-items-center bg-white p-2 rounded shadow-sm border">
            <div class="input-group input-group-sm mr-3">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-light border-0"><i class="fas fa-calendar-alt text-dark"></i></span>
                </div>
                <input type="date" id="fecha" name="fecha" value="{{ $fecha }}" class="form-control border-0 font-weight-bold text-dark" style="background-color: #f8f9fa;" onchange="this.form.submit()">
            </div>
            <a href="{{ route('reportes.diario.pdf', ['fecha' => $fecha]) }}" class="btn-saas-pdf" target="_blank">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
        </form>
    </div>
@stop

@section('content')
<div class="container-fluid pb-5">

    {{-- 1. KPIs --}}
    <div class="row">
        <div class="col-lg-3 col-6 mb-3">
            <div class="kpi-card-vibrant kpi-bg-ventas">
                <div class="kpi-vibrant-title">Total Ventas (Bruto)</div>
                <div class="kpi-vibrant-amount">${{ number_format($totalVendido ?? 0, 0, ',', '.') }}</div>
                @if(($totalDescuentos ?? 0) > 0)
                    <div class="badge-vibrant">
                        <i class="fas fa-tags mr-1"></i> Ahorro: -${{ number_format($totalDescuentos, 0, ',', '.') }}
                    </div>
                @endif
                <i class="fas fa-chart-line kpi-vibrant-icon"></i>
            </div>
        </div>
        <div class="col-lg-3 col-6 mb-3">
            <div class="kpi-card-vibrant kpi-bg-efectivo">
                <div class="kpi-vibrant-title">Efectivo Ingresado</div>
                <div class="kpi-vibrant-amount">${{ number_format($totalEfectivo ?? 0, 0, ',', '.') }}</div>
                <i class="fas fa-money-bill-wave kpi-vibrant-icon"></i>
            </div>
        </div>
        <div class="col-lg-3 col-6 mb-3">
            <div class="kpi-card-vibrant kpi-bg-nequi">
                <div class="kpi-vibrant-title">Transferencias / Nequi</div>
                <div class="kpi-vibrant-amount">${{ number_format($totalNequi ?? 0, 0, ',', '.') }}</div>
                <i class="fas fa-mobile-alt kpi-vibrant-icon"></i>
            </div>
        </div>
        <div class="col-lg-3 col-6 mb-3">
            <div class="kpi-card-vibrant kpi-bg-fiados">
                <div class="kpi-vibrant-title">Nuevas Deudas (Fiados)</div>
                <div class="kpi-vibrant-amount">${{ number_format($totalFiadoNuevo ?? 0, 0, ',', '.') }}</div>
                <i class="fas fa-file-invoice-dollar kpi-vibrant-icon"></i>
            </div>
        </div>
    </div>

    {{-- 2. LIQUIDACIÓN Y CUADRE DE CAJA --}}
    <div class="card-dark-premium">
        <div class="card-header-dark">
            <div class="bg-success text-white rounded p-2 mr-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                <i class="fas fa-cash-register"></i>
            </div>
            <h3 class="dark-section-title">Liquidación Financiera (Caja Física)</h3>
        </div>
        <div class="card-body py-4">
            <div class="row align-items-center justify-content-center text-center">
                
                {{-- Base Inicial --}}
                <div class="col-md-2 col-6 mb-3">
                    <div class="dark-label-title">Base Inicial</div>
                    <div class="dark-amount-display dark-text-base">${{ number_format($baseInicial ?? 0, 0, ',', '.') }}</div>
                </div>

                <div class="col-auto mb-3 d-none d-md-block"><span class="dark-math-operator" style="color: #34D399;">+</span></div>

                {{-- Efectivo Ingresado --}}
                <div class="col-md-2 col-6 mb-3">
                    <div class="dark-label-title">Efectivo Ingresado</div>
                    <div class="dark-amount-display dark-text-success">+${{ number_format($totalEfectivo ?? 0, 0, ',', '.') }}</div>
                </div>

                <div class="col-auto mb-3 d-none d-md-block"><span class="dark-math-operator">-</span></div>

                {{-- Sueldo Cajero --}}
                <div class="col-md-2 col-6 mb-3">
                    <div class="dark-label-title">Sueldo Cajero</div>
                    <div class="dark-amount-display dark-text-danger">-${{ number_format($sueldo ?? 0, 0, ',', '.') }}</div>
                </div>

                <div class="col-auto mb-3 d-none d-md-block"><span class="dark-math-operator">-</span></div>

                {{-- Gastos --}}
                <div class="col-md-2 col-6 mb-3">
                    <div class="dark-label-title">Gastos / Compras</div>
                    <div class="dark-amount-display dark-text-danger">-${{ number_format($totalGastos ?? 0, 0, ',', '.') }}</div>
                </div>

                <div class="col-auto mb-3 d-none d-md-block"><span class="dark-math-operator">-</span></div>

                {{-- Fondo Sig. Turno --}}
                <div class="col-md-2 col-6 mb-3">
                    <div class="dark-label-title">Fondo Sig. Turno</div>
                    <div class="dark-amount-display dark-text-warning">-${{ number_format($baseSiguiente ?? 0, 0, ',', '.') }}</div>
                </div>

                <div class="col-auto mb-3 d-none d-lg-block"><span class="dark-math-operator" style="color: #10B981;">=</span></div>

                {{-- Efectivo a Entregar --}}
                <div class="col-lg-3 col-md-6 col-12 mt-3 mt-lg-0">
                    <div class="glowing-box">
                        <div class="glowing-label">Efectivo a Entregar</div>
                        <div class="glowing-amount">${{ number_format($dineroEntregado ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- 3. AUDITORÍA DE INVENTARIO --}}
    <div class="card card-custom">
        <div class="card-header-custom d-flex align-items-center">
            <div class="bg-primary text-white rounded p-1 mr-2 d-inline-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                <i class="fas fa-boxes" style="font-size: 0.9rem;"></i>
            </div>
            <h3 class="section-title">Auditoría de Inventario</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-modern text-center">
                    <thead class="thead-light">
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
                                $fisico = $item->stock_fisico_cierre !== null ? $item->stock_fisico_cierre : ($item->stock_esperado ?? 0);
                                $diferencia = $fisico - ($item->stock_esperado ?? 0);
                            @endphp
                            <tr>
                                <td class="text-left pl-4 font-weight-bold">{{ $item->producto->nombre ?? 'Producto' }} <span class="text-secondary font-weight-bold" style="font-size: 0.85rem;">({{ $item->producto->categoria->nombre ?? 'Sin Categoría' }})</span></td>
                                <td><span class="text-muted">{{ $item->stock_esperado ?? 0 }}</span></td>
                                <td><span class="badge badge-secondary px-3 py-2" style="font-size: 0.9rem;">{{ $fisico }}</span></td>
                                <td>
                                    <span class="amount-display {{ $diferencia < 0 ? 'text-danger' : ($diferencia > 0 ? 'text-warning' : 'text-success') }}">
                                        {{ $diferencia > 0 ? '+' : '' }}{{ $diferencia }}
                                    </span>
                                </td>
                                <td>
                                    @if($diferencia < 0)
                                        <span class="badge badge-saas-danger px-2 py-1"><i class="fas fa-exclamation-circle mr-1"></i> Faltante</span>
                                    @elseif($diferencia > 0)
                                        <span class="badge badge-saas-warning px-2 py-1"><i class="fas fa-exclamation-triangle mr-1"></i> Sobrante</span>
                                    @else
                                        <span class="badge badge-saas-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Exacto</span>
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
                <div class="card card-custom h-100">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="bg-primary text-white rounded p-1 mr-2 d-inline-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                                <i class="fas fa-tags" style="font-size: 0.9rem;"></i>
                            </span>
                            <h3 class="section-title">
                                VENTA DE {{ strtoupper($categoria->nombre) }}
                            </h3>
                        </div>
                        <div class="text-right">
                            @if(($categoria->total_descuento ?? 0) > 0)
                                <span class="badge badge-warning text-dark font-weight-bold mr-1" title="Ahorro en Promociones">
                                    <i class="fas fa-percent mr-1"></i> Ahorro: -${{ number_format($categoria->total_descuento, 0, ',', '.') }}
                                </span>
                            @endif
                            <span class="badge badge-light font-weight-bold px-2 py-1" style="font-size: 0.9rem;">
                                Total: ${{ number_format($categoria->total_vendido ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-modern text-center mb-0">
                                <thead class="thead-light">
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
                                                ${{ number_format($item->precio_unitario ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                @if(($item->descuento ?? 0) > 0)
                                                    <span class="badge badge-saas-warning font-weight-bold px-2 py-1 text-dark">
                                                        <i class="fas fa-tag mr-1"></i>Promo (-${{ number_format($item->descuento, 0, ',', '.') }})
                                                    </span>
                                                @elseif($item->tiene_promo ?? false)
                                                    <span class="badge badge-saas-warning font-weight-bold px-2 py-1 text-dark">
                                                        <i class="fas fa-tag mr-1"></i>Promo
                                                    </span>
                                                @else
                                                    <span class="badge badge-light border text-muted px-2 py-1">Normal</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->metodo_pago === 'efectivo')
                                                    <span class="badge badge-saas-success px-2 py-1">
                                                        <i class="fas fa-money-bill-wave mr-1"></i>Efectivo
                                                    </span>
                                                @elseif(in_array($item->metodo_pago, ['transferencia', 'nequi']))
                                                    <span class="badge badge-saas-info px-2 py-1">
                                                        <i class="fas fa-mobile-alt mr-1"></i>Transf.
                                                    </span>
                                                @elseif($item->metodo_pago === 'fiado')
                                                    <span class="badge badge-saas-danger px-2 py-1">
                                                        <i class="fas fa-user-clock mr-1"></i>Fiado
                                                    </span>
                                                @else
                                                    <span class="badge badge-light border px-2 py-1 text-dark font-weight-bold">
                                                        {{ ucfirst($item->metodo_pago ?? 'Otro') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-right pr-3 font-weight-bold amount-display" style="color: #059669;">
                                                ${{ number_format($item->subtotal ?? 0, 0, ',', '.') }}
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

    {{-- CONTROL DE FIADOS --}}
    <div class="row">
        {{-- Fiados Nuevos --}}
        <div class="col-md-6 mb-4">
            <div class="card card-custom h-100">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="bg-danger text-white rounded p-1 mr-2 d-inline-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                            <i class="fas fa-user-tag" style="font-size: 0.9rem;"></i>
                        </span>
                        <h3 class="section-title">Fiados Nuevos</h3>
                    </div>
                    <span class="badge badge-danger font-weight-bold px-2 py-1">Total: ${{ number_format($totalFiadoNuevo ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-modern text-center mb-0">
                            <thead class="thead-light">
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
                                            ${{ number_format($fiado->total ?? 0, 0, ',', '.') }}
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
            <div class="card card-custom h-100">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <span class="bg-success text-white rounded p-1 mr-2 d-inline-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                            <i class="fas fa-hand-holding-usd" style="font-size: 0.9rem;"></i>
                        </span>
                        <h3 class="section-title">Abonos a Fiados (Ingreso a Caja)</h3>
                    </div>
                    <span class="badge badge-success font-weight-bold px-2 py-1">Total: ${{ number_format($totalCobradoFiados ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-modern text-center mb-0">
                            <thead class="thead-light">
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
                                            @if(($cobro->metodo_pago_saldo ?? '') === 'transferencia' || in_array($cobro->metodo_pago ?? '', ['transferencia', 'nequi']))
                                                <span class="badge badge-saas-info px-2 py-1 font-weight-bold">Transf.</span>
                                            @else
                                                <span class="badge badge-saas-success px-2 py-1 font-weight-bold">Efectivo</span>
                                            @endif
                                        </td>
                                        <td class="text-right pr-4 font-weight-bold amount-display" style="color: #059669; font-size: 1.05rem;">
                                            ${{ number_format($cobro->total ?? 0, 0, ',', '.') }}
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

    {{-- 5. OBSERVACIONES Y NOVEDADES DEL TURNO --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card card-custom">
                <div class="card-header-custom d-flex align-items-center">
                    <div class="bg-warning text-dark rounded p-1 mr-2 d-inline-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                        <i class="fas fa-comment-alt" style="font-size: 0.9rem;"></i>
                    </div>
                    <h3 class="section-title">Observaciones y Novedades del Turno</h3>
                </div>
                <div class="card-body p-3">
                    <div class="p-3 bg-light rounded border text-dark">
                        @if(!empty($observaciones ?? ($turno->observaciones ?? null)))
                            <p class="mb-0 font-weight-normal" style="white-space: pre-line; font-size: 0.95rem; color: #334155; line-height: 1.5;">
                                {{ $observaciones ?? $turno->observaciones }}
                            </p>
                        @else
                            <p class="mb-0 text-muted font-italic" style="font-size: 0.9rem;">
                                <i class="fas fa-info-circle mr-1"></i> No se registraron observaciones ni novedades en este turno.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@stop