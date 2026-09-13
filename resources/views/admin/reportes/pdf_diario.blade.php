<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Diario - Coctelería Evolushion</title>
    <style>
        @page {
            margin: 8mm 10mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #e2e8f0;
            background-color: #1a1a24;
            margin: 0;
            padding: 5px;
        }

        /* Encabezado Principal */
        .header-card {
            background-color: #2d1b4e;
            border: 1px solid #6b21a8;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 8px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td { vertical-align: middle; }
        
        .logo-container {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #a855f7;
            overflow: hidden;
        }
        .logo-container img { width: 100%; height: 100%; object-fit: cover; }
        
        .title-main { font-size: 14pt; font-weight: bold; color: #ffffff; text-transform: uppercase; margin: 0; }
        .title-sub { font-size: 8pt; color: #c084fc; font-weight: bold; margin-top: 2px; }

        /* Banner de información general del turno */
        .info-bar {
            width: 100%;
            border-collapse: collapse;
            background-color: #222232;
            border: 1px solid #334155;
            border-radius: 4px;
            margin-bottom: 8px;
        }
        .info-bar td {
            padding: 5px 10px;
            width: 33.33%;
            border-right: 1px solid #334155;
        }
        .info-bar td:last-child { border-right: none; }
        .label-sm { font-size: 6.5pt; color: #94a3b8; text-transform: uppercase; font-weight: bold; display: block; }
        .val-sm { font-size: 8.5pt; font-weight: bold; color: #f8fafc; }

        /* KPIs Resumen superior */
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px 0;
            margin-bottom: 8px;
        }
        .kpi-card {
            background-color: #262636;
            border-radius: 4px;
            padding: 5px 8px;
            border-left: 3px solid #a855f7;
        }
        .kpi-label { font-size: 6.5pt; color: #94a3b8; text-transform: uppercase; font-weight: bold; }
        .kpi-value { font-size: 10pt; font-weight: bold; color: #ffffff; }

        /* Estructura a dos columnas */
        .col-table { width: 100%; border-collapse: collapse; }
        .col-table td { vertical-align: top; }

        /* Contenedores de secciones */
        .card-box {
            background-color: #222232;
            border: 1px solid #334155;
            border-radius: 5px;
            margin-bottom: 8px;
            padding: 8px;
        }
        .card-title {
            font-size: 8.5pt;
            font-weight: bold;
            color: #38bdf8;
            text-transform: uppercase;
            border-bottom: 1px solid #334155;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .title-purple { color: #c084fc; }
        .title-emerald { color: #34d399; }
        .title-amber { color: #fbbf24; }

        /* Tablas de datos */
        .data-table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        .data-table th {
            background-color: #181826;
            color: #94a3b8;
            text-transform: uppercase;
            font-size: 6.5pt;
            padding: 4px;
            border-bottom: 1px solid #334155;
            text-align: left;
        }
        .data-table td { padding: 4px; border-bottom: 1px solid #2a2a3c; color: #cbd5e1; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; color: #ffffff; }

        /* Badges */
        .badge-small { padding: 2px 4px; border-radius: 3px; font-size: 6pt; font-weight: bold; }
        .badge-promo { background-color: #4c1d95; color: #e9d5ff; }
        .badge-nequi { background-color: #1e3a8a; color: #bfdbfe; }
        .badge-efectivo { background-color: #064e3b; color: #a7f3d0; }

        /* Cuadre de caja */
        .calc-table { width: 100%; border-collapse: collapse; font-size: 8pt; }
        .calc-table td { padding: 4px 6px; border-bottom: 1px solid #2a2a3c; }
        .highlight-green { background-color: #064e3b; color: #6ee7b7; font-weight: bold; }
        .highlight-purple { background-color: #3b0764; color: #f3e8ff; font-weight: bold; font-size: 8.5pt; }

        .obs-box {
            background-color: #181826;
            border: 1px dashed #475569;
            padding: 6px 8px;
            border-radius: 4px;
            font-size: 8pt;
            color: #f1f5f9;
            font-style: italic;
        }
    </style>
</head>
<body>

    <!-- ENCABEZADO PRINCIPAL -->
    <div class="header-card">
        <table class="header-table">
            <tr>
                <td style="width: 55px;">
                    @if(!empty($logoBase64))
                        <div class="logo-container">
                            <img src="{{ $logoBase64 }}" alt="Evolushion Logo">
                        </div>
                    @else
                        <div class="logo-container" style="background-color: #0f0a1c; text-align: center; line-height: 45px; font-size: 16pt; color: #d8b4fe;">🍸</div>
                    @endif
                </td>
                <td>
                    <div class="title-main">Coctelería Evolushion</div>
                    <div class="title-sub">Reporte Diario de Caja y Cuadre de Turno</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <span style="background-color: #3b0764; border: 1px solid #a855f7; color: #f3e8ff; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 8pt;">
                        📅 {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <!-- DATOS GENERALES DEL TURNO -->
    <table class="info-bar">
        <tr>
            <td>
                <span class="label-sm">Atendido por / Empleado</span>
                <span class="val-sm">{{ $turno->user->name ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="label-sm">Hora de Apertura</span>
                <span class="val-sm">{{ isset($turno->fecha_inicio) ? \Carbon\Carbon::parse($turno->fecha_inicio)->format('h:i A') : 'N/A' }}</span>
            </td>
            <td>
                <span class="label-sm">Hora de Cierre</span>
                <span class="val-sm">{{ isset($turno->fecha_cierre) ? \Carbon\Carbon::parse($turno->fecha_cierre)->format('h:i A') : 'En curso / Abierto' }}</span>
            </td>
        </tr>
    </table>

    <!-- TARJETAS RESUMEN KPI -->
    <table class="kpi-table">
        <tr>
            <td style="width: 20%;">
                <div class="kpi-card">
                    <div class="kpi-label">Ventas Cócteles</div>
                    <div class="kpi-value">$ {{ number_format($totalCocteles ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card" style="border-left-color: #06b6d4;">
                    <div class="kpi-label">Ventas Nevera</div>
                    <div class="kpi-value">$ {{ number_format($totalNevera ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card" style="border-left-color: #10b981;">
                    <div class="kpi-label">Ingreso Total</div>
                    <div class="kpi-value">$ {{ number_format(($totalEfectivo ?? 0) + ($totalNequi ?? 0), 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card" style="border-left-color: #f59e0b;">
                    <div class="kpi-label">Efectivo Caja</div>
                    <div class="kpi-value">$ {{ number_format($totalEfectivo ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card" style="border-left-color: #f43f5e;">
                    <div class="kpi-label">Entregado Final</div>
                    <div class="kpi-value">$ {{ number_format((($turno->base_inicial ?? 0) + ($totalEfectivo ?? 0)) - (($turno->sueldo_empleado ?? 0) + ($totalGastos ?? 0)), 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- DISPOSICIÓN PRINCIPAL A DOS COLUMNAS -->
    <table class="col-table">
        <tr>
            <!-- COLUMNA IZQUIERDA -->
            <td style="width: 52%; padding-right: 4px;">
                
                <!-- 1. VENTAS POR PRECIO (CÓCTELES) -->
                <div class="card-box">
                    <div class="card-title title-purple">🍸 1. Ventas por Precio (Cócteles)</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Precio Unid.</th>
                                <th class="text-center">Cant.</th>
                                <th class="text-center">Promociones</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coctelesAgrupados ?? [] as $c)
                            <tr>
                                <td class="fw-bold">$ {{ number_format($c['precio'], 0, ',', '.') }}</td>
                                <td class="text-center fw-bold">{{ $c['cant'] }}</td>
                                <td class="text-center">
                                    @if(($c['promo'] ?? '-') != '-')
                                        <span class="badge-small badge-promo">{{ $c['promo'] }}</span>
                                    @else
                                        <span style="color: #64748b;">-</span>
                                    @endif
                                </td>
                                <td class="text-right fw-bold">$ {{ number_format($c['sub'], 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center" style="color: #64748b;">No hay registro de ventas de cócteles.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- 2. CONTROL DE STOCK Y PRODUCTOS DE NEVERA -->
                <div class="card-box">
                    <div class="card-title">🥤 2. Ventas y Movimiento de Stock</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Inicial</th>
                                <th class="text-center">Vend.</th>
                                <th class="text-center">Final</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                       <tbody>
    @forelse($detallesTurno ?? [] as $detalle)
        <tr>
            <td>{{ data_get($detalle, 'producto.nombre', 'N/A') }}</td>
            <td class="text-center">{{ data_get($detalle, 'stock_inicial', '-') }}</td>
            <td class="text-center fw-bold" style="color:#38bdf8;">{{ data_get($detalle, 'cantidad_vendida', 0) }}</td>
            <td class="text-center fw-bold">{{ data_get($detalle, 'stock_final', '-') }}</td>
            <td class="text-right fw-bold">
                $ {{ number_format(data_get($detalle, 'subtotal_vendido', 0), 0, ',', '.') }}
            </td>
        </tr>
    @empty
        @forelse($productosVendidos ?? [] as $pv)
            <tr>
                <td>{{ data_get($pv, 'producto.nombre', data_get($pv, 'nombre', 'N/A')) }}</td>
                <td class="text-center">-</td>
                <td class="text-center fw-bold" style="color:#38bdf8;">{{ data_get($pv, 'total_cantidad', 0) }}</td>
                <td class="text-center">-</td>
                <td class="text-right fw-bold">
                    $ {{ number_format(data_get($pv, 'total_monto', 0), 0, ',', '.') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center" style="color: #64748b;">No se registraron ventas de productos.</td>
            </tr>
        @endforelse
    @endforelse
</tbody>
                    </table>
                </div>

                <!-- 3. FIADOS Y CUENTAS PENDIENTES -->
                <div class="card-box">
                    <div class="card-title title-amber">📝 3. Fiados y Cuentas Pendientes</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Cliente / Deudor</th>
                                <th>Detalle / Producto</th>
                                <th class="text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fiadosDetalle ?? [] as $f)
                            <tr>
                                <td><strong style="color: #fca5a5;">{{ $f['cliente'] }}</strong></td>
                                <td>{{ $f['detalle'] }}</td>
                                <td class="text-right fw-bold" style="color: #f87171;">$ {{ number_format($f['monto'], 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center" style="color: #64748b;">Sin registros de fiados en este turno.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </td>

            <!-- COLUMNA DERECHA -->
            <td style="width: 48%; padding-left: 4px;">

                <!-- 4. INVENTARIO DE NEVERA (STOCK GENERAL) -->
                <div class="card-box">
                    <div class="card-title title-emerald">❄️ 4. Inventario General (Stock)</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Stock</th>
                                <th>Producto</th>
                                <th class="text-center">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($stockNevera ?? []) as $chunk)
                            <tr>
                                <td>{{ $chunk[0]['nombre'] ?? '-' }}</td>
                                <td class="text-center fw-bold {{ ($chunk[0]['stock'] ?? 0) == 0 ? 'text-muted' : '' }}">
                                    {{ $chunk[0]['stock'] ?? '-' }}
                                </td>
                                <td>{{ $chunk[1]['nombre'] ?? '-' }}</td>
                                <td class="text-center fw-bold {{ ($chunk[1]['stock'] ?? 0) == 0 ? 'text-muted' : '' }}">
                                    {{ $chunk[1]['stock'] ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center" style="color: #64748b;">No hay inventario cargado.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- 5. CONTROL DE VASOS E INSUMOS -->
                <div class="card-box">
                    <div class="card-title">🥤 5. Control de Insumos / Vasos</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th class="text-center">Existencia</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($insumosVasos ?? [] as $v)
                            <tr>
                                <td>{{ $v['nombre'] }}</td>
                                <td class="text-center fw-bold">{{ $v['cantidad'] }}</td>
                                <td><span class="badge-small badge-efectivo">OK</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td>Vasos 12 oz</td><td class="text-center fw-bold">46</td><td><span class="badge-small badge-efectivo">OK</span></td>
                            </tr>
                            <tr>
                                <td>Vasos 14 oz</td><td class="text-center fw-bold">132 / 85</td><td><span class="badge-small badge-efectivo">OK</span></td>
                            </tr>
                            <tr>
                                <td>Vasos 16 oz</td><td class="text-center fw-bold">155</td><td><span class="badge-small badge-efectivo">OK</span></td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- 6. CUADRE DE CAJA DIARIA -->
                <div class="card-box">
                    <div class="card-title title-purple">💰 6. Cuadre de Caja Diaria</div>
                    <table class="calc-table">
                        <tr>
                            <td>Base Inicial:</td>
                            <td class="text-right fw-bold">$ {{ number_format($turno->base_inicial ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Efectivo Recaudado:</td>
                            <td class="text-right fw-bold">$ {{ number_format($totalEfectivo ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="highlight-green">
                            <td>TOTAL RECAUDO EFECTIVO:</td>
                            <td class="text-right">$ {{ number_format(($turno->base_inicial ?? 0) + ($totalEfectivo ?? 0), 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Transferencias Nequi:</td>
                            <td class="text-right fw-bold" style="color:#60a5fa;">$ {{ number_format($totalNequi ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Total Fiados / Crédito:</td>
                            <td class="text-right fw-bold" style="color:#f87171;">$ {{ number_format($totalFiados ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Gastos / Egresos:</td>
                            <td class="text-right fw-bold" style="color:#f87171;">- $ {{ number_format($totalGastos ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Sueldo Empleado:</td>
                            <td class="text-right fw-bold" style="color:#f87171;">- $ {{ number_format($turno->sueldo_empleado ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="highlight-purple">
                            <td>EFECTIVO A ENTREGAR:</td>
                            <td class="text-right">$ {{ number_format((($turno->base_inicial ?? 0) + ($totalEfectivo ?? 0)) - (($turno->sueldo_empleado ?? 0) + ($totalGastos ?? 0)), 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>

            </td>
        </tr>
    </table>

    <!-- 7. OBSERVACIONES -->
    @if(!empty($turno->observaciones) || isset($observaciones))
    <div class="card-box" style="margin-top: 2px;">
        <div class="card-title title-amber" style="margin-bottom: 4px;">⚠️ Observaciones y Novedades del Turno</div>
        <div class="obs-box">
            "{{ $turno->observaciones ?? $observaciones }}"
        </div>
    </div>
    @endif

</body>
</html>