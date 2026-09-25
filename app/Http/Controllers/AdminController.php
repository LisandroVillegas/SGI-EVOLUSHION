<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\Turno;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();

        // 1. Ventas totales del día actual (basado en created_at)
        $ventasHoy = Venta::whereDate('created_at', $hoy)->sum('total');

        // 2. Estado del turno actual (si hay alguno abierto)
        $turnoActual = Turno::with('user')->where('estado', 'abierto')->first();
        
        $tiempoTurno = null;
        if ($turnoActual) {
            $inicio = Carbon::parse($turnoActual->fecha_inicio);
            $tiempoTurno = $inicio->diffForHumans(null, true); // Ej: "3 horas", "45 minutos"
        }

        // 3. Total de fiados pendientes por cobrar
        $fiadosPendientes = Venta::where('estado_pago', 'pendiente')->sum('total');

        // 4. Productos con stock crítico (5 o menos)
        $stockCriticoUmbral = 5;
        $cantidadStockCritico = Producto::where('stock', '<=', $stockCriticoUmbral)->count();

        // Tabla 1: 5 productos con el stock más bajo
        $productosBajoStock = Producto::with('categoria')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        // Gráfico: Ventas de los últimos 7 días
        $ultimos7Dias = [];
        $ventas7Dias = [];
        
        // Iteramos de hace 6 días hasta hoy
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $ultimos7Dias[] = $fecha->format('d M'); // Ej: "24 Sep"
            
            $totalDia = Venta::whereDate('created_at', $fecha)->sum('total');
            $ventas7Dias[] = $totalDia;
        }

        // Tabla 2: Últimos 5 movimientos registrados (Ventas y Compras)
        $ultimasVentas = Venta::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($venta) {
                return [
                    'tipo' => 'Venta',
                    'descripcion' => 'Venta - ' . ucfirst($venta->metodo_pago),
                    'monto' => $venta->total,
                    'fecha' => $venta->created_at,
                    'icono' => 'fas fa-arrow-up',
                    'color' => 'success'
                ];
            });

        $ultimasCompras = Compra::orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($compra) {
                return [
                    'tipo' => 'Gasto/Compra',
                    'descripcion' => $compra->concepto ?? 'Compra de inventario',
                    'monto' => $compra->total,
                    'fecha' => $compra->created_at,
                    'icono' => 'fas fa-arrow-down',
                    'color' => 'danger'
                ];
            });

        // Unimos las colecciones, ordenamos por fecha descendente y tomamos solo 5
        $ultimosMovimientos = $ultimasVentas->concat($ultimasCompras)
            ->sortByDesc('fecha')
            ->take(5);

        return view('admin.dashboard.index', compact(
            'ventasHoy', 
            'turnoActual', 
            'tiempoTurno',
            'fiadosPendientes', 
            'cantidadStockCritico',
            'productosBajoStock',
            'ultimos7Dias',
            'ventas7Dias',
            'ultimosMovimientos'
        ));
    }
}
