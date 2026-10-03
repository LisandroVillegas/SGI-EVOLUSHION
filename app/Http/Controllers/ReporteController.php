<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Turno;
use App\Models\TurnoDetalle;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Compra;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'fecha' => 'nullable|date_format:Y-m-d'
        ]);

        $fecha = $request->input('fecha', Carbon::today()->format('Y-m-d'));
        $data = $this->obtenerDatosReporte($fecha);

        return view('admin.reportes.diario', $data);
    }

    public function exportarPdf(Request $request)
    {
        $request->validate([
            'fecha' => 'nullable|date_format:Y-m-d'
        ]);

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
                'totalDescuentos'      => 0,
                'totalFiadoNuevo'      => 0,
                'totalFiados'          => 0,
                'fiadosDelDia'         => collect(),
                'cobrosFiadosDia'      => collect(),
                'totalCobradoFiados'   => 0,
                'totalGastos'          => 0,
                'gastos'               => 0,
                'categorias'           => Categoria::orderBy('nombre', 'asc')->get()->map(function($c) {
                    $c->ventas = collect();
                    $c->total_vendido = 0;
                    $c->total_descuento = 0;
                    return $c;
                }),
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
        // FIADOS COBRADOS EN EL PERÍODO / TURNO
        // ==========================================
        if ($turno) {
            $cobrosFiadosDia = Venta::with(['user', 'detalles.producto'])
                ->where('estado_pago', 'pagado')
                ->where('metodo_pago', 'fiado')
                ->where(function($query) use ($turno, $inicioDia, $finDia) {
                    $query->where('turno_pago_id', $turno->id)
                          ->orWhere(function($sub) use ($inicioDia, $finDia) {
                              $sub->whereNotNull('fecha_pago')
                                  ->whereBetween('fecha_pago', [$inicioDia, $finDia]);
                          })
                          ->orWhere(function($subLegacy) use ($inicioDia, $finDia) {
                              $subLegacy->whereNull('turno_pago_id')
                                        ->whereNotNull('cliente_fiado')
                                        ->whereBetween('updated_at', [$inicioDia, $finDia])
                                        ->whereColumn('updated_at', '>', 'created_at');
                          });
                })
                ->get();
        } else {
            $cobrosFiadosDia = Venta::with(['user', 'detalles.producto'])
                ->where('estado_pago', 'pagado')
                ->where('metodo_pago', 'fiado')
                ->where(function($query) use ($fecha, $inicioDia, $finDia) {
                    $query->whereBetween('fecha_pago', [$inicioDia, $finDia])
                          ->orWhereDate('fecha_pago', $fecha)
                          ->orWhere(function($subLegacy) use ($fecha) {
                              $subLegacy->whereNull('turno_pago_id')
                                        ->whereNotNull('cliente_fiado')
                                        ->whereDate('updated_at', $fecha)
                                        ->whereColumn('updated_at', '>', 'created_at');
                          });
                })
                ->get();
        }

        $totalCobradoFiados = $cobrosFiadosDia->sum('total');

        // Ventas normales de contado del período (Efectivo y Transferencias inmediatas)
        $ventasContadoHoy = $ventas->where('estado_pago', 'pagado')
            ->where('metodo_pago', '!=', 'fiado');

        // Totales financieros
        $totalVendido = $ventasContadoHoy->sum('total') + $totalCobradoFiados;

        // Efectivo Recibido (Sólo dinero físico en caja)
        $efectivoVentasHoy = $ventasContadoHoy->where('metodo_pago', 'efectivo')->sum('total') 
            + $ventasContadoHoy->where('metodo_pago', 'mixto')->sum('pago_efectivo');
            
        $efectivoCobrosFiados = $cobrosFiadosDia->where('metodo_pago_saldo', 'efectivo')->sum('total')
            ?: ($cobrosFiadosDia->sum('pago_efectivo') ?: $cobrosFiadosDia->where('metodo_pago', 'efectivo')->sum('total'));
        $totalEfectivo = $efectivoVentasHoy + $efectivoCobrosFiados;

        // Transferencias / Nequi (Dinero electrónico bancario)
        $nequiVentasHoy = $ventasContadoHoy->whereIn('metodo_pago', ['transferencia', 'nequi'])->sum('total') 
            + $ventasContadoHoy->where('metodo_pago', 'mixto')->sum('pago_transferencia');

        $nequiCobrosFiados = $cobrosFiadosDia->where('metodo_pago_saldo', 'transferencia')->sum('total')
            ?: ($cobrosFiadosDia->sum('pago_transferencia') ?: $cobrosFiadosDia->whereIn('metodo_pago', ['transferencia', 'nequi'])->sum('total'));
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

        // 4. Categorías dinámicas y sus ventas detalladas en el turno
        $categorias = Categoria::when(in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses(Categoria::class)), function ($q) {
                $q->withTrashed();
            })
            ->orderBy('nombre', 'asc')
            ->get();

        // Obtener detalles de venta del período/turno con relaciones necesarias
        $detallesVentas = VentaDetalle::whereHas('venta', function ($q) use ($inicioDia, $finDia) {
                $q->whereBetween('created_at', [$inicioDia, $finDia]);
            })
            ->with(['producto.categoria', 'venta'])
            ->get();

        // Calcular total general de descuentos otorgados por promociones en el turno
        $totalDescuentos = $detallesVentas->sum(function ($det) {
            $precioBase = $det->precio_unitario ?? ($det->producto->precio_venta ?? 0);
            $subtotalTeorico = $det->cantidad * $precioBase;
            return max(0, $subtotalTeorico - $det->subtotal);
        });

        // Asociar a cada categoría sus ventas detalladas con método de pago, producto y promociones
        $categorias = $categorias->map(function ($categoria) use ($detallesVentas) {
            $detallesCategoria = $detallesVentas->filter(function ($det) use ($categoria) {
                return $det->producto && $det->producto->categoria_id == $categoria->id;
            });

            // Agrupamos para mostrar consolidado por producto, método de pago y aplicación de promoción
            $categoria->ventas = $detallesCategoria->groupBy(function ($d) {
                $precioBase = $d->precio_unitario ?? ($d->producto->precio_venta ?? 0);
                $descuento = ($d->cantidad * $precioBase) - $d->subtotal;
                $tienePromo = ($descuento > 0 || ($d->venta && $d->venta->aplica_promocion));
                $metodo = $d->venta ? $d->venta->metodo_pago : 'efectivo';
                return $d->producto_id . '_' . $metodo . '_' . ($tienePromo ? '1' : '0');
            })->map(function ($grupo) {
                $primerItem = $grupo->first();
                $cantidadTotal = $grupo->sum('cantidad');
                $subtotalTotal = $grupo->sum('subtotal');
                $precioBase = $primerItem->precio_unitario ?? ($primerItem->producto->precio_venta ?? 0);
                $descuentoTotal = max(0, ($cantidadTotal * $precioBase) - $subtotalTotal);

                return (object) [
                    'producto'        => $primerItem->producto,
                    'producto_nombre' => $primerItem->producto->nombre ?? 'N/A',
                    'cantidad'        => $cantidadTotal,
                    'precio_unitario' => $precioBase,
                    'descuento'       => $descuentoTotal,
                    'tiene_promo'     => $descuentoTotal > 0 || ($primerItem->venta && $primerItem->venta->aplica_promocion),
                    'metodo_pago'     => $primerItem->venta ? $primerItem->venta->metodo_pago : 'efectivo',
                    'subtotal'        => $subtotalTotal,
                ];
            })->values();

            $categoria->total_vendido = $categoria->ventas->sum('subtotal');
            $categoria->total_descuento = $categoria->ventas->sum('descuento');

            return $categoria;
        });

        // Si se usa SoftDeletes, descartar categorías borradas que NO hayan tenido ventas en este turno
        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses(Categoria::class))) {
            $categorias = $categorias->filter(function ($cat) {
                return !$cat->trashed() || $cat->ventas->isNotEmpty();
            });
        }

        // 5. Inventario de Insumos (Vasos)
        $inventarioVasos = Producto::where('nombre', 'LIKE', '%Vaso%')->get();

        // 6. Gastos del período (Sincronizado con el módulo de turnos)
        $totalGastos = 0;
        
        if ($turno) {
            $totalGastos = Compra::where('turno_id', $turno->id)->sum('total');
        } 
        
        if ($totalGastos == 0) {
            $totalGastos = Compra::whereBetween('created_at', [$inicioDia, $finDia])->sum('total');
        }

        // 7. Extracción correcta de columnas de la tabla 'turnos'
        $baseInicial   = ($turno && isset($turno->base_caja)) ? $turno->base_caja : 0;
        $sueldo        = ($turno && isset($turno->sueldo)) ? $turno->sueldo : 0;
        
        // AQUÍ ESTABLECEMOS LA BASE SIGUIENTE DINÁMICA: Lee la guardada en el turno o toma la base inicial por defecto si está abierto
        $baseSiguiente = ($turno && isset($turno->base_siguiente_turno) && $turno->base_siguiente_turno !== null) 
                         ? $turno->base_siguiente_turno 
                         : $baseInicial; 
        
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
            'totalDescuentos'      => $totalDescuentos,
            'totalFiadoNuevo'      => $totalFiadoNuevo,
            'totalFiados'          => $totalFiadoNuevo,
            'fiadosDelDia'         => $fiadosDelDia,
            'cobrosFiadosDia'      => $cobrosFiadosDia,
            'totalCobradoFiados'   => $totalCobradoFiados,
            'totalGastos'          => $totalGastos,
            'gastos'               => $totalGastos,
            'categorias'           => $categorias,
            'inventarioVasos'      => $inventarioVasos,
            'baseInicial'          => $baseInicial,
            'sueldo'               => $sueldo,
            'baseSiguiente'        => $baseSiguiente,
            'dineroEntregado'      => $dineroEntregado,
            'observaciones'        => $turno->observaciones ?? null,
            'totalTransacciones'   => $ventas->count(),
            'promocionesAplicadas' => $ventas->where('aplica_promocion', true)->count(),
        ];
    }
}