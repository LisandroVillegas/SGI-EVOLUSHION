@extends('adminlte::page')

@section('title', 'Dashboard Principal')

@section('content_header')
    <h1 class="m-0 text-dark">Dashboard Principal</h1>
@stop

@section('content')
    <!-- 4 Tarjetas de Indicadores Principales (Small Boxes) -->
    <div class="row">
        <!-- Ventas Hoy -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>${{ number_format($ventasHoy, 0, ',', '.') }}</h3>
                    <p>Ventas del Día</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="{{ route('ventas.index') }}" class="small-box-footer">Ver ventas <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Estado Turno -->
        <div class="col-lg-3 col-6">
            <div class="small-box {{ $turnoActual ? 'bg-info' : 'bg-secondary' }}">
                <div class="inner">
                    @if($turnoActual)
                        <h3>Abierto</h3>
                        <p>Por {{ $turnoActual->user->name }} (hace {{ $tiempoTurno }})</p>
                    @else
                        <h3>Cerrado</h3>
                        <p>Sin turno activo</p>
                    @endif
                </div>
                <div class="icon">
                    <i class="fas fa-cash-register"></i>
                </div>
                <a href="{{ route('turnos.index') }}" class="small-box-footer">Ir a caja <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Fiados Pendientes -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>${{ number_format($fiadosPendientes, 0, ',', '.') }}</h3>
                    <p>Fiados por Cobrar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <a href="{{ route('ventas.index') }}?estado=pendiente" class="small-box-footer">Ver fiados <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <!-- Stock Crítico -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $cantidadStockCritico }}</h3>
                    <p>Productos en Stock Crítico</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('producto.index') }}" class="small-box-footer">Ver inventario <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Columna Izquierda: Gráficos y Movimientos -->
        <div class="col-lg-7">
            <!-- Gráfico de Ventas (Últimos 7 días) -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Ventas de los últimos 7 días
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
            
            <!-- Últimos Movimientos -->
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exchange-alt mr-1"></i>
                        Últimos 5 Movimientos
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-valign-middle m-0">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Fecha y Hora</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ultimosMovimientos as $movimiento)
                                <tr>
                                    <td>
                                        <span class="badge badge-{{ $movimiento['color'] }} px-2 py-1">
                                            <i class="{{ $movimiento['icono'] }} mr-1"></i>
                                            {{ $movimiento['tipo'] }}
                                        </span>
                                    </td>
                                    <td>{{ $movimiento['descripcion'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($movimiento['fecha'])->format('d/m/Y h:i A') }}</td>
                                    <td class="text-{{ $movimiento['color'] }} font-weight-bold">
                                        ${{ number_format($movimiento['monto'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No hay movimientos registrados.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Stock -->
        <div class="col-lg-5">
            <!-- Tabla de Productos con Bajo Stock -->
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-box-open mr-1"></i>
                        Atención: Stock Bajo (Top 5)
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped m-0">
                        <thead>
                            <tr>
                                <th class="pl-3">Producto</th>
                                <th>Categoría</th>
                                <th class="text-center pr-3">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productosBajoStock as $producto)
                            <tr>
                                <td class="pl-3">{{ $producto->nombre }}</td>
                                <td>{{ $producto->categoria->nombre ?? 'Sin Categoría' }}</td>
                                <td class="text-center pr-3">
                                    <span class="badge {{ $producto->stock <= 0 ? 'badge-danger' : 'badge-warning' }} px-2 py-1" style="font-size: 0.9rem;">
                                        {{ $producto->stock }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Todos los productos tienen buen stock.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('producto.index') }}" class="text-uppercase font-weight-bold">Ver todos los productos</a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obtenemos los datos inyectados por Blade
        var labels = {!! json_encode($ultimos7Dias) !!};
        var data = {!! json_encode($ventas7Dias) !!};
        
        // Contexto del Canvas
        var ctx = document.getElementById('salesChart').getContext('2d');
        
        // Configuración de Chart.js
        new Chart(ctx, {
            type: 'bar', // Gráfico de barras para las ventas diarias
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ventas ($)',
                    backgroundColor: 'rgba(40, 167, 69, 0.75)', // Color verde (success)
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1,
                    borderRadius: 4, // Bordes redondeados en las barras
                    data: data
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 }).format(value);
                            }
                        },
                        grid: {
                            borderDash: [5, 5],
                            color: '#e9ecef'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // Ocultamos la leyenda ya que es una sola métrica
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += '$' + new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@stop
