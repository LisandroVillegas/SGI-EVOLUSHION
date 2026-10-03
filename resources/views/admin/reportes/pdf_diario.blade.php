<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Evolushion - Reporte Operacional de Turno - {{ $fecha }}</title>
    <style>
        @page { margin: 25px 30px; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #2d3748; margin: 0; padding: 0; line-height: 1.3; }
        .container { width: 100%; margin: 0 auto; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-weight-bold { font-weight: bold; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-primary { color: #007bff; }
        .text-warning { color: #d39e00; }
        .text-muted { color: #6c757d; }
        
        /* Header Corporativo */
        .header-table { width: 100%; border-bottom: 2px solid #2d3748; padding-bottom: 8px; margin-bottom: 12px; }
        .header-table td { vertical-align: middle; }
        .logo-img { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; }
        .header-title { font-size: 18px; font-weight: 900; margin: 0; text-transform: uppercase; color: #1a202c; letter-spacing: -0.5px; }
        .header-subtitle { font-size: 11px; color: #4a5568; margin-top: 3px; }
        
        /* Badges */
        .badge { display: inline-block; padding: 2px 5px; font-size: 9px; font-weight: bold; border-radius: 3px; text-transform: uppercase; }
        .badge-success { background-color: #d1f2e1; color: #0f5132; }
        .badge-primary { background-color: #cff4fc; color: #055160; }
        .badge-danger { background-color: #f8d7da; color: #842029; }
        .badge-warning { background-color: #FEF08A; color: #713F12; border: 1px solid #FDE047; font-weight: 700; }
        .badge-secondary { background-color: #F8FAFC; color: #64748B; border: 1px solid #E2E8F0; }

        /* Cuadro Resumen de Totales */
        .kpi-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .kpi-table td { width: 25%; padding: 10px; border: 1px solid #E2E8F0; text-align: center; border-radius: 4px; }
        .kpi-title { font-size: 9px; text-transform: uppercase; font-weight: bold; color: #64748B; margin-bottom: 4px; display: block; letter-spacing: 0.5px; }
        .kpi-val { font-size: 16px; font-weight: 800; }
        
        /* Section Titles */
        .section-title { 
            background-color: #F8FAFC; 
            color: #1E293B; 
            padding: 8px 10px; 
            font-size: 11px; 
            font-weight: 800;
            text-transform: uppercase; 
            margin-top: 18px; 
            margin-bottom: 8px;
            border-bottom: 2px solid #E2E8F0;
            letter-spacing: 0.5px;
        }

        /* Cuadre de Caja Special */
        .cuadre-box { border: 1px solid #E2E8F0; border-radius: 4px; margin-bottom: 18px; overflow: hidden; }
        .cuadre-header { background-color: #0F172A; color: white; padding: 8px; font-size: 11px; font-weight: bold; text-align: center; text-transform: uppercase; letter-spacing: 0.5px; }
        .cuadre-table { width: 100%; border-collapse: collapse; text-align: center; }
        .cuadre-table td { padding: 10px 5px; width: 20%; border-right: 1px solid #E2E8F0; }
        .cuadre-table td:last-child { border-right: none; background-color: #ECFDF5; }
        .cuadre-label { font-size: 9px; color: #64748B; text-transform: uppercase; display: block; margin-bottom: 4px; font-weight: bold; }
        .cuadre-value { font-size: 13px; font-weight: bold; color: #334155; }
        .cuadre-value-total { font-size: 18px; font-weight: 800; color: #059669; }

        /* Tablas */
        .table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .table th, .table td { border-bottom: 1px solid #E2E8F0; padding: 6px 8px; font-size: 10px; }
        .table th { background-color: #F8FAFC; color: #64748B; text-transform: uppercase; font-weight: bold; font-size: 9px; border-bottom: 2px solid #E2E8F0; text-align: left; }
        .table-striped tr:nth-child(even) { background-color: #F8FAFC; }
        
        .cat-header { background-color: #F1F5F9; color: #1E293B; padding: 6px 8px; font-weight: 800; font-size: 10px; text-transform: uppercase; border-bottom: 2px solid #CBD5E1; }

        /* Observaciones */
        .obs-box { border: 1px solid #E2E8F0; background-color: #F8FAFC; padding: 10px; border-radius: 4px; margin-top: 10px; }
        .obs-title { font-size: 10px; font-weight: 800; color: #1E293B; text-transform: uppercase; margin-bottom: 5px; padding-bottom: 3px; }

    </style>
</head>
<body>
    <div class="container">
        
        <!-- HEADER CORPORATIVO -->
        <table class="header-table">
            <tr>
                <td style="width: 20%;">
                    @if(isset($logoBase64) && $logoBase64 != '')
                        <img src="{{ $logoBase64 }}" class="logo-img" alt="Logo">
                    @else
                        <h2 style="margin: 0; color: #1a202c; font-size: 18px;">EVOLUSHION</h2>
                    @endif
                </td>
                <td class="text-center" style="width: 55%;">
                    <div class="header-title">Evolushion - Reporte Operacional de Turno</div>
                    <div class="header-subtitle">
                        Fecha: <strong>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</strong> | 
                        Turno #{{ $turno ? sprintf('%06d', $turno->id) : 'N/A' }} 
                        ({{ $turno && $turno->fecha_inicio ? \Carbon\Carbon::parse($turno->fecha_inicio)->format('H:i') : '--:--' }} a 
                         {{ $turno && $turno->fecha_cierre ? \Carbon\Carbon::parse($turno->fecha_cierre)->format('H:i') : 'En curso' }})
                    </div>
                </td>
                <td class="text-right" style="width: 25%;">
                    <div style="font-size: 9px; color: #718096;">Impresión: {{ date('d/m/Y H:i') }}</div>
                    <div style="font-size: 9px; color: #718096; margin-top: 3px;">
                        Responsable:<br><strong style="color: #2d3748;">{{ $turno ? ($turno->user->name ?? 'Sistema') : 'No Registrado' }}</strong>
                    </div>
                </td>
            </tr>
        </table>

        <!-- 1. CUADRO RESUMEN DE TOTALES OPERACIONALES -->
        <table class="kpi-table">
            <tr>
                <td style="background-color: #f8fafc;">
                    <span class="kpi-title">Total Ventas (Bruto)</span>
                    <span class="kpi-val" style="color: #1a202c;">${{ number_format($totalVendido, 0, ',', '.') }}</span>
                </td>
                <td style="background-color: #f0fdf4;">
                    <span class="kpi-title">Total Efectivo Ingresado</span>
                    <span class="kpi-val text-success">${{ number_format($totalEfectivo, 0, ',', '.') }}</span>
                </td>
                <td style="background-color: #eff6ff;">
                    <span class="kpi-title">Total Transferencias / Nequi</span>
                    <span class="kpi-val text-primary">${{ number_format($totalNequi, 0, ',', '.') }}</span>
                </td>
                <td style="background-color: #fffbeb;">
                    <span class="kpi-title">Total Descuentos Promos</span>
                    <span class="kpi-val text-warning">-${{ number_format($totalDescuentos ?? 0, 0, ',', '.') }}</span>
                </td>
            </tr>
        </table>

        <!-- 2. LIQUIDACIÓN DE CAJA FÍSICA -->
        <div class="cuadre-box">
            <div class="cuadre-header">LIQUIDACIÓN FINANCIERA (EFECTIVO NETO EN GAVETA)</div>
            <table class="cuadre-table">
                <tr>
                    <td>
                        <span class="cuadre-label">Base Inicial</span>
                        <span class="cuadre-value">${{ number_format($baseInicial ?? 0, 0, ',', '.') }}</span>
                    </td>
                    <td>
                        <span class="cuadre-label">Sueldo Cajero</span>
                        <span class="cuadre-value text-danger">-${{ number_format($sueldo, 0, ',', '.') }}</span>
                    </td>
                    <td>
                        <span class="cuadre-label">Gastos / Compras</span>
                        <span class="cuadre-value text-danger">-${{ number_format($totalGastos, 0, ',', '.') }}</span>
                    </td>
                    <td>
                        <span class="cuadre-label">Fondo Sig. Turno</span>
                        <span class="cuadre-value text-danger">-${{ number_format($baseSiguiente, 0, ',', '.') }}</span>
                    </td>
                    <td>
                        <span class="cuadre-label text-success">TOTAL FÍSICO A ENTREGAR</span>
                        <span class="cuadre-value-total">${{ number_format($dineroEntregado, 0, ',', '.') }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- 3. DESGLOSE DINÁMICO DE VENTAS POR CATEGORÍA -->
        <div class="section-title">DESGLOSE DE VENTAS POR CATEGORÍA</div>
        @forelse($categorias as $categoria)
            <table class="table table-striped" style="margin-bottom: 10px;">
                <thead>
                    <tr>
                        <th colspan="4" class="cat-header" style="text-align: left;">
                            VENTA DE {{ strtoupper($categoria->nombre) }}
                        </th>
                        <th colspan="2" class="cat-header" style="text-align: right;">
                            @if(($categoria->total_descuento ?? 0) > 0)
                                <span style="font-size: 9px; margin-right: 8px; opacity: 0.9;">
                                    (Ahorro: -${{ number_format($categoria->total_descuento, 0, ',', '.') }})
                                </span>
                            @endif
                            Total: ${{ number_format($categoria->total_vendido ?? 0, 0, ',', '.') }}
                        </th>
                    </tr>
                    <tr>
                        <th class="text-left" style="width: 32%;">Producto</th>
                        <th class="text-center" style="width: 8%;">Cant.</th>
                        <th class="text-right" style="width: 15%; text-align: right;">Precio Base</th>
                        <th class="text-center" style="width: 17%; text-align: center;">Promoción</th>
                        <th class="text-center" style="width: 13%; text-align: center;">Método de Pago</th>
                        <th class="text-right" style="width: 15%; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categoria->ventas as $item)
                        <tr>
                            <td class="text-left font-weight-bold">{{ $item->producto_nombre }}</td>
                            <td class="text-center font-weight-bold">{{ $item->cantidad }}</td>
                            <td class="text-right" style="text-align: right;">${{ number_format($item->precio_unitario, 0, ',', '.') }}</td>
                            <td class="text-center" style="text-align: center;">
                                @if($item->descuento > 0)
                                    <span class="badge badge-warning">Promo (-${{ number_format($item->descuento, 0, ',', '.') }})</span>
                                @elseif($item->tiene_promo)
                                    <span class="badge badge-warning">Promo</span>
                                @else
                                    <span class="badge badge-secondary">Normal</span>
                                @endif
                            </td>
                            <td class="text-center" style="text-align: center;">
                                @if($item->metodo_pago === 'efectivo')
                                    <span class="badge badge-success">Efectivo</span>
                                @elseif(in_array($item->metodo_pago, ['transferencia', 'nequi']))
                                    <span class="badge badge-primary">Transf.</span>
                                @elseif($item->metodo_pago === 'fiado')
                                    <span class="badge badge-danger">Fiado</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($item->metodo_pago) }}</span>
                                @endif
                            </td>
                            <td class="text-right text-success font-weight-bold" style="text-align: right; color: #059669;">
                                ${{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted" style="padding: 8px;">
                                Sin ventas registradas en este turno.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @empty
            <table class="table">
                <tr><td class="text-center text-muted" style="padding: 10px;">Sin categorías registradas en el sistema.</td></tr>
            </table>
        @endforelse

        <!-- 4. MOVIMIENTOS DE FIADOS -->
        <div class="section-title">CONTROL DE FIADOS</div>
        <table style="width: 100%; border-collapse: collapse;" cellpadding="0" cellspacing="0">
            <tr>
                <!-- Fiados Nuevos -->
                <td style="width: 48%; vertical-align: top;">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th colspan="2" style="background-color: #e53e3e; color: #ffffff; text-align: left;">
                                    Fiados Nuevos (Otorgados Hoy)
                                </th>
                            </tr>
                            <tr>
                                <th class="text-left">Cliente / Deudor</th>
                                <th class="text-right">Monto Deuda</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fiadosDelDia as $fiado)
                            <tr>
                                <td class="text-left font-weight-bold">{{ $fiado->cliente_fiado ?? 'Sin Nombre' }}</td>
                                <td class="text-right text-danger font-weight-bold" style="text-align: right; color: #E11D48;">${{ number_format($fiado->total, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center text-muted">No se otorgaron fiados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
                <td style="width: 4%;"></td>
                <!-- Fiados Cobrados -->
                <td style="width: 48%; vertical-align: top;">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th colspan="3" style="background-color: #38a169; color: #ffffff; text-align: left;">
                                    Abonos a Fiados (Ingreso a Caja)
                                </th>
                            </tr>
                            <tr>
                                <th class="text-left">Cliente</th>
                                <th class="text-center">Medio</th>
                                <th class="text-right">Abono</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cobrosFiadosDia as $cobro)
                            <tr>
                                <td class="text-left font-weight-bold">{{ $cobro->cliente_fiado ?? 'Cliente' }}</td>
                                <td class="text-center">
                                    <span class="badge {{ ($cobro->metodo_pago_saldo === 'transferencia' || in_array($cobro->metodo_pago, ['transferencia', 'nequi'])) ? 'badge-primary' : 'badge-success' }}">
                                        {{ ($cobro->metodo_pago_saldo === 'transferencia' || in_array($cobro->metodo_pago, ['transferencia', 'nequi'])) ? 'Transf.' : 'Efectivo' }}
                                    </span>
                                </td>
                                <td class="text-right text-success font-weight-bold" style="text-align: right; color: #059669;">${{ number_format($cobro->total, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted">No se cobraron fiados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <!-- 5. INVENTARIO FÍSICO -->
        <div class="section-title">CONTROL DE INVENTARIO (FÍSICO VS SISTEMA)</div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="text-left" style="width: 35%;">Nombre del Producto</th>
                    <th class="text-center" style="width: 20%;">Stock Esperado (Teórico)</th>
                    <th class="text-center" style="width: 20%;">Stock Físico (Declarado)</th>
                    <th class="text-center" style="width: 25%;">Diferencia / Resultado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($detallesTurno as $item)
                    @php
                        $fisico = $item->stock_fisico_cierre !== null ? $item->stock_fisico_cierre : $item->stock_esperado;
                        $diferencia = $fisico - ($item->stock_esperado ?? 0);
                    @endphp
                <tr>
                    <td class="text-left font-weight-bold">{{ $item->producto->nombre ?? 'Producto' }} <span style="color: #4a5568; font-weight: normal; font-size: 9px;">({{ $item->producto->categoria->nombre ?? 'Sin Categoría' }})</span></td>
                    <td class="text-center">{{ $item->stock_esperado ?? 0 }}</td>
                    <td class="text-center font-weight-bold">{{ $fisico }}</td>
                    <td class="text-center font-weight-bold">
                        @if($diferencia < 0)
                            <span class="text-danger">Faltante: {{ abs($diferencia) }}</span>
                        @elseif($diferencia > 0)
                            <span class="text-warning">Sobrante: +{{ $diferencia }}</span>
                        @else
                            <span class="text-success">Cuadre Exacto</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted">El inventario no se ha contabilizado aún.</td></tr>
                @endforelse
            </tbody>
        </table>

        <!-- 6. OBSERVACIONES -->
        @if(!empty($observaciones))
        <div class="obs-box">
            <div class="obs-title">Observaciones del Turno / Cierre de Caja</div>
            <div style="font-size: 10px; white-space: pre-line; color: #2d3748;">{{ $observaciones }}</div>
        </div>
        @endif

    </div>
</body>
</html>