<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Turno;
use App\Models\TurnoDetalle;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Compra;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->input('fecha', Carbon::today()->format('Y-m-d'));
        $data = $this->obtenerDatosReporte($fecha);

        return view('admin.reportes.diario', $data);
    }

    public function exportarPdf(Request $request)
    {
        $fecha = $request->input('fecha', Carbon::today()->format('Y-m-d'));
        $data = $this->obtenerDatosReporte($fecha);

        // Apunta al archivo exacto con extensión .jpg
        $pathLogo = public_path('vendor/adminlte/dist/img/AdminLTELogo.jpg');

        // Respaldo por si está dentro de public/public
        if (!file_exists($pathLogo)) {
            $pathLogo = public_path('public/vendor/adminlte/dist/img/AdminLTELogo.jpg');
        }

        $logoBase64 = '';
        if (file_exists($pathLogo)) {
            $type = pathinfo($pathLogo, PATHINFO_EXTENSION);
            $dataImage = file_get_contents($pathLogo);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($dataImage);
        }

        $data['logoBase64'] = $logoBase64;

        $pdf = Pdf::loadView('admin.reportes.pdf_diario', $data);
        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream('Reporte_Diario_' . $fecha . '.pdf');
    }

   private function obtenerDatosReporte($fecha)
    {
        // 1. PRIORIDAD ABSOLUTA: Si hay un turno ABIERTO actualmente, lo tomamos sin importar la fecha del calendario
        $turno = Turno::with('user')->whereNull('fecha_cierre')->latest('fecha_inicio')->first();

        // 2. Si NO hay un turno abierto, buscamos los turnos cerrados que correspondan a la fecha consultada
        if (!$turno) {
            $turno = Turno::with('user')
                ->whereNotNull('fecha_cierre')
                ->where(function($sub) use ($fecha) {
                    $sub->whereDate('fecha_inicio', '<=', $fecha)
                        ->whereDate('fecha_cierre', '>=', $fecha);
                })
                ->latest('fecha_inicio')
                ->first();
        }

        // Definir el rango de tiempo para ventas, compras y gastos
        if ($turno) {
            if (!$turno->fecha_cierre) {
                // Si el turno está ABIERTO, abarca desde que inició el turno hasta ahora mismo
                $inicioDia = Carbon::parse($turno->fecha_inicio);
                $finDia = Carbon::now();
            } else {
                // Si el turno está CERRADO, usamos el rango exacto de ese turno
                $inicioDia = Carbon::parse($turno->fecha_inicio);
                $finDia = Carbon::parse($turno->fecha_cierre);
            }
        } else {
            // Si definitivamente no hay turno de ningún tipo para esa fecha
            $inicioDia = Carbon::parse($fecha)->startOfDay();
            $finDia = Carbon::parse($fecha)->endOfDay();
        }

        // Si definitivamente no hay turno que aplicar, devolvemos ceros
        if (!$turno) {
            return [
                'fecha'                => $fecha,
                'turno'                => null,
                'turnos'               => collect(),
                'detallesTurno'        => collect(),
                'ventas'               => collect(),
                'totalVendido'         => 0,
                'totalEfectivo'        => 0,
                'totalNequi'           => 0,
                'totalTransferencia'   => 0,
                'totalFiadoNuevo'      => 0,
                'totalFiados'          => 0,
                'fiadosDelDia'         => collect(),
                'cobrosFiadosDia'      => collect(),
                'totalCobradoFiados'   => 0,
                'totalGastos'          => 0,
                'gastos'               => 0,
                'coctelesPorPrecio'    => collect(),
                'inventarioNevera'     => Producto::with('categoria')->whereHas('categoria', function ($q) { $q->where('nombre', 'LIKE', '%nevera%'); })->get(),
                'ventasNevera'         => collect(),
                'efectivoNevera'       => 0,
                'inventarioVasos'      => Producto::where('nombre', 'LIKE', '%Vaso%')->get(),
                'baseInicial'          => 0,
                'sueldo'               => 0,
                'baseSiguiente'        => 0,
                'dineroEntregado'      => 0,
                'observaciones'        => null,
                'totalTransacciones'   => 0,
                'promocionesAplicadas' => 0,
            ];
        }

        // Obtener la lista completa de turnos para el día seleccionado
        $turnos = Turno::with('user')
            ->whereBetween('fecha_inicio', [Carbon::parse($fecha)->startOfDay(), Carbon::parse($fecha)->endOfDay()])
            ->get();

        // 2. Ventas creadas en el rango dinámico del turno/día
        $ventas = Venta::with(['user', 'detalles.producto'])
            ->whereBetween('created_at', [$inicioDia, $finDia])
            ->orderBy('id', 'desc')
            ->get();

        // Fiados creados en el período que siguen pendientes
        $fiadosDelDia = $ventas->where('metodo_pago', 'fiado')->where('estado_pago', 'pendiente');
        $totalFiadoNuevo = $fiadosDelDia->sum('total');

        // ==========================================
        // FIADOS COBRADOS HOY 
        // ==========================================
        $cobrosFiadosDia = Venta::with(['user', 'detalles.producto'])
            ->where('estado_pago', 'pagado')
            ->where(function($query) use ($fecha, $inicioDia) {
                $query->whereDate('updated_at', $fecha)
                      ->orWhereBetween('updated_at', [$inicioDia, Carbon::now()]);
            })
            ->get()
            ->filter(function($venta) use ($inicioDia) {
                if (Carbon::parse($venta->created_at)->lt($inicioDia)) {
                    return true;
                }
                return Carbon::parse($venta->created_at)->gte($inicioDia) && Carbon::parse($venta->updated_at)->gt($venta->created_at);
            });

        $totalCobradoFiados = $cobrosFiadosDia->sum('total');

        // Ventas normales de contado del período
        $ventasContadoHoy = $ventas->where('estado_pago', 'pagado')
            ->filter(function($venta) use ($inicioDia) {
                if (Carbon::parse($venta->created_at)->gte($inicioDia) && Carbon::parse($venta->updated_at)->gt($venta->created_at)) {
                    return false; 
                }
                return true;
            });

        // Totales financieros
        $totalVendido = $ventasContadoHoy->sum('total') + $totalCobradoFiados;

        // Efectivo Recibido
        $efectivoVentasHoy = $ventasContadoHoy->where('metodo_pago', 'efectivo')->sum('total') 
            + $ventasContadoHoy->where('metodo_pago', 'mixto')->sum('pago_efectivo');
            
        $efectivoCobrosFiados = $cobrosFiadosDia->where('metodo_pago', 'efectivo')->sum('total')
            + $cobrosFiadosDia->where('metodo_pago', 'mixto')->sum('pago_efectivo');
        $totalEfectivo = $efectivoVentasHoy + $efectivoCobrosFiados;

        // Transferencias / Nequi
        $nequiVentasHoy = $ventasContadoHoy->whereIn('metodo_pago', ['transferencia', 'nequi'])->sum('total') 
            + $ventasContadoHoy->where('metodo_pago', 'mixto')->sum('pago_transferencia');

        $nequiCobrosFiados = $cobrosFiadosDia->whereIn('metodo_pago', ['transferencia', 'nequi'])->sum('total')
            + $cobrosFiadosDia->where('metodo_pago', 'mixto')->sum('pago_transferencia');
        $totalNequi = $nequiVentasHoy + $nequiCobrosFiados;

        // 3. Control de Stock del Turno
        if ($turno) {
            $turno->load(['compras.detalles.producto', 'ventas.detalles.producto']);

            $detallesTurno = TurnoDetalle::with('producto')
                ->where('turno_id', $turno->id)
                ->get();

            foreach ($detallesTurno as $detalle) {
                $comprasDelProducto = 0;
                $ventasDelProducto = 0;
                
                if ($turno->compras) {
                    foreach ($turno->compras as $compra) {
                        if ($compra->detalles) {
                            $comprasDelProducto += $compra->detalles->where('producto_id', $detalle->producto_id)->sum('cantidad');
                        }
                    }
                }

                if ($turno->ventas) {
                    foreach ($turno->ventas as $venta) {
                        if ($venta->detalles) {
                            $ventasDelProducto += $venta->detalles->where('producto_id', $detalle->producto_id)->sum('cantidad');
                        }
                    }
                }

                $cantidadApertura = $detalle->stock_fisico_apertura ?? $detalle->cantidad ?? $detalle->cantidad_inicial ?? 0;
                
                $detalle->stock_esperado = ($cantidadApertura + $comprasDelProducto) - $ventasDelProducto;
                $detalle->cantidad_vendida = $ventasDelProducto;
                $detalle->cantidad_apertura = $cantidadApertura;
            }
        } else {
            $detallesTurno = collect();
        }

        // 4. Agrupación de Cócteles por Rango de Precio
        $coctelesPorPrecio = VentaDetalle::whereHas('venta', function ($q) use ($inicioDia, $finDia) {
                $q->whereBetween('created_at', [$inicioDia, $finDia]);
            })
            ->whereHas('producto.categoria', function ($q) {
                $q->where('nombre', 'LIKE', '%coctel%');
            })
            ->select(
                'precio_unitario',
                DB::raw('SUM(cantidad) as total_cantidad'),
                DB::raw('SUM(subtotal) as total_monto')
            )
            ->groupBy('precio_unitario')
            ->orderBy('precio_unitario', 'asc')
            ->get();

        // 5. Inventario y Ventas de Nevera
        $inventarioNevera = Producto::with('categoria')
            ->whereHas('categoria', function ($q) {
                $q->where('nombre', 'LIKE', '%nevera%');
            })->get();

        $ventasNevera = VentaDetalle::whereHas('venta', function ($q) use ($inicioDia, $finDia) {
                $q->whereBetween('created_at', [$inicioDia, $finDia]);
            })
            ->whereHas('producto.categoria', function ($q) {
                $q->where('nombre', 'LIKE', '%nevera%');
            })
            ->select('producto_id', DB::raw('SUM(cantidad) as cantidad'), DB::raw('SUM(subtotal) as total'))
            ->groupBy('producto_id')
            ->with('producto')
            ->get();

        $efectivoNevera = $ventasNevera->sum('total');

        // 6. Inventario de Insumos (Vasos)
        $inventarioVasos = Producto::where('nombre', 'LIKE', '%Vaso%')->get();

        // 7. Gastos del período (Sincronizado con el módulo de turnos)
        $totalGastos = 0;
        
        if ($turno) {
            $totalGastos = Compra::where('turno_id', $turno->id)->sum('total');
        } 
        
        if ($totalGastos == 0) {
            $totalGastos = Compra::whereBetween('created_at', [$inicioDia, $finDia])->sum('total');
        }

        // 8. Extracción correcta de columnas de la tabla 'turnos'
        $baseInicial   = ($turno && isset($turno->base_caja)) ? $turno->base_caja : 0;
        $sueldo        = ($turno && isset($turno->sueldo)) ? $turno->sueldo : 0;
        $baseSiguiente = 0; 
        
        // Liquidación de Cuadre de Caja
        $dineroEntregado = ($baseInicial + $totalEfectivo) - ($totalGastos + $sueldo + $baseSiguiente);

        return [
            'fecha'                => $fecha,
            'turno'                => $turno,
            'turnos'               => $turnos,
            'detallesTurno'        => $detallesTurno,
            'ventas'               => $ventas,
            'totalVendido'         => $totalVendido,
            'totalEfectivo'        => $totalEfectivo,
            'totalNequi'           => $totalNequi,
            'totalTransferencia'   => $totalNequi,
            'totalFiadoNuevo'      => $totalFiadoNuevo,
            'totalFiados'          => $totalFiadoNuevo,
            'fiadosDelDia'         => $fiadosDelDia,
            'cobrosFiadosDia'      => $cobrosFiadosDia,
            'totalCobradoFiados'   => $totalCobradoFiados,
            'totalGastos'          => $totalGastos,
            'gastos'               => $totalGastos,
            'coctelesPorPrecio'    => $coctelesPorPrecio,
            'inventarioNevera'     => $inventarioNevera,
            'ventasNevera'         => $ventasNevera,
            'efectivoNevera'       => $efectivoNevera,
            'inventarioVasos'      => $inventarioVasos,
            'baseInicial'          => $baseInicial,
            'sueldo'               => $sueldo,
            'baseSiguiente'        => $baseSiguiente,
            'dineroEntregado'      => $dineroEntregado,
            'observaciones'        => $turno->notas ?? null,
            'totalTransacciones'   => $ventas->count(),
            'promocionesAplicadas' => $ventas->where('aplica_promocion', true)->count(),
        ];
    }
}