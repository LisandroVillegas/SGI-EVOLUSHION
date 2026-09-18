<?php

namespace App\Http\Controllers;

use App\Models\VentaDetalle;
use App\Models\Turno;
use App\Models\Producto;
use App\Models\TurnoDetalle;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TurnoController extends Controller
{
    public function index()
    {
        $turnos = Turno::with(['user', 'ventas', 'compras'])->orderBy('id', 'desc')->get();
        return view('admin.turnos.index', compact('turnos'));
    }

    public function create()
    {
        $turnoActivo = Turno::where('user_id', Auth::id())
                            ->where('estado', 'abierto')
                            ->first();

        if ($turnoActivo) {
            return redirect()->route('turnos.index')
                ->with('mensaje', 'Ya tienes un turno activo en este momento.')
                ->with('icono', 'info');
        }

        $productos = Producto::orderBy('nombre', 'asc')->get();

        return view('admin.turnos.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'base_caja' => 'required|numeric|min:0',
            'notas' => 'nullable|string',
            'productos' => 'required|array',
        ]);

        $turnoActivo = Turno::where('user_id', Auth::id())
                            ->where('estado', 'abierto')
                            ->first();

        if ($turnoActivo) {
            return redirect()->route('turnos.index')
                ->with('mensaje', 'No puedes abrir otro turno porque ya tienes uno activo.')
                ->with('icono', 'error');
        }

        try {
            DB::beginTransaction();

            $turno = new Turno();
            $turno->user_id = Auth::id();
            $turno->fecha_inicio = Carbon::now();
            $turno->base_caja = $request->base_caja;
            $turno->estado = 'abierto';
            $turno->notas = $request->notas;
            $turno->save();

            foreach ($request->productos as $prod) {
                $stockSistema = (int)$prod['stock_sistema'];
                $stockFisico = (int)$prod['stock_fisico'];
                $diferencia = $stockFisico - $stockSistema;

                TurnoDetalle::create([
                    'turno_id' => $turno->id,
                    'producto_id' => $prod['id'],
                    'stock_sistema_apertura' => $stockSistema,
                    'stock_fisico_apertura' => $stockFisico,
                    'diferencia_apertura' => $diferencia,
                ]);
            }

            DB::commit();

            return redirect()->route('turnos.index')
                ->with('mensaje', 'Turno e inventario inicial registrados exitosamente')
                ->with('icono', 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('mensaje', 'Error al abrir el turno: ' . $e->getMessage())
                ->with('icono', 'error');
        }
    }

    public function show($id)
    {
        $turno = Turno::with([
            'detalles.producto', 
            'user', 
            'compras.detalles.producto', 
            'ventas.detalles.producto',
            'cobrosFiados'
        ])->findOrFail($id);
        
        $totalCompras = $turno->compras ? $turno->compras->sum('total') : 0;
        
        // Ventas de contado en efectivo de este turno (excluye ventas fiadas)
        $totalVentasEfectivo = $turno->ventas ? $turno->ventas->where('metodo_pago', '!=', 'fiado')->whereNull('cliente_fiado')->sum('pago_efectivo') : 0;
        $totalVentasTransferencia = $turno->ventas ? $turno->ventas->where('metodo_pago', '!=', 'fiado')->whereNull('cliente_fiado')->sum('pago_transferencia') : 0;
        
        $ventasFiadas = $turno->ventas ? $turno->ventas->where('metodo_pago', 'fiado') : collect();
        $totalFiadoOtorgado = $ventasFiadas->sum('total');

        // Fiados cobrados en este turno (soporta tanto los nuevos con turno_pago_id como los históricos)
        $cobrosFiadosNuevos = $turno->cobrosFiados ?? collect();
        $cobrosFiadosAntiguos = $turno->ventas ? $turno->ventas->where('estado_pago', 'pagado')->whereNotNull('cliente_fiado')->whereNull('turno_pago_id') : collect();
        $cobrosFiados = $cobrosFiadosNuevos->merge($cobrosFiadosAntiguos)->unique('id');
        $totalFiadoCobrado = $cobrosFiados->sum('pago_efectivo');

        $totalVentas = $turno->ventas ? $turno->ventas->sum('total') : 0;
        
        if ($turno->estado === 'abierto') {
            $pagoTrabajadora = 0;
            $dineroEsperado = ($turno->base_caja + $totalVentasEfectivo + $totalFiadoCobrado) - $totalCompras;
            $efectivoReal = 0;
            $diferenciaCalculada = 0;
        } else {
            $pagoTrabajadora = !is_null($turno->sueldo) ? $turno->sueldo : 0;
            $dineroEsperado = !is_null($turno->total_efectivo_esperado) 
                ? $turno->total_efectivo_esperado 
                : (($turno->base_caja + $totalVentasEfectivo + $totalFiadoCobrado) - $totalCompras - $pagoTrabajadora);
                
            $efectivoReal = !is_null($turno->total_efectivo_real) ? $turno->total_efectivo_real : 0;
            $diferenciaCalculada = !is_null($turno->total_descuadre_dinero) 
                ? $turno->total_descuadre_dinero 
                : ($efectivoReal - $dineroEsperado);
        }

        return view('admin.turnos.show', compact(
            'turno', 
            'totalCompras', 
            'pagoTrabajadora', 
            'totalVentasEfectivo', 
            'totalVentasTransferencia', 
            'totalFiadoOtorgado',
            'totalFiadoCobrado',
            'totalVentas',
            'dineroEsperado',
            'efectivoReal',
            'diferenciaCalculada'
        ));
    }

    public function edit($id)
    {
        $turno = Turno::with([
            'detalles.producto', 
            'user',
            'compras.detalles.producto',
            'ventas.detalles.producto',
            'cobrosFiados'
        ])->findOrFail($id);

        if ($turno->estado !== 'abierto') {
            return redirect()->route('turnos.index')
                ->with('mensaje', 'Este turno ya se encuentra cerrado.')
                ->with('icono', 'info');
        }

        $totalCompras = $turno->compras ? $turno->compras->sum('total') : 0;
        $pagoTrabajadora = $turno->sueldo ?? 0;
        $totalVentasEfectivo = $turno->ventas ? $turno->ventas->where('metodo_pago', '!=', 'fiado')->whereNull('cliente_fiado')->sum('pago_efectivo') : 0;

        $ventasFiadas = $turno->ventas ? $turno->ventas->where('metodo_pago', 'fiado') : collect();
        $totalFiadoOtorgado = $ventasFiadas->sum('total');

        // Fiados cobrados en este turno
        $cobrosFiadosNuevos = $turno->cobrosFiados ?? collect();
        $cobrosFiadosAntiguos = $turno->ventas ? $turno->ventas->where('estado_pago', 'pagado')->whereNotNull('cliente_fiado')->whereNull('turno_pago_id') : collect();
        $cobrosFiados = $cobrosFiadosNuevos->merge($cobrosFiadosAntiguos)->unique('id');
        $totalFiadoCobrado = $cobrosFiados->sum('pago_efectivo');

        $dineroEsperado = ($turno->base_caja + $totalVentasEfectivo + $totalFiadoCobrado) - $totalCompras - $pagoTrabajadora;

        $detalleComprasTexto = [];
        if ($turno->compras) {
            foreach ($turno->compras as $compra) {
                if ($compra->detalles) {
                    foreach ($compra->detalles as $det) {
                        $nombreProd = $det->producto ? $det->producto->nombre : 'Producto';
                        $subtotal = $det->subtotal ?? ($det->precio_compra * $det->cantidad);
                        $detalleComprasTexto[] = "{$det->cantidad}x {$nombreProd} ($" . number_format($subtotal, 0, ',', '.') . ")";
                    }
                }
            }
        }

        foreach ($turno->detalles as $detalle) {
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

            $detalle->stock_esperado_calculado = ($detalle->stock_fisico_apertura + $comprasDelProducto) - $ventasDelProducto;
        }

        return view('admin.turnos.edit', compact(
            'turno', 
            'totalCompras', 
            'pagoTrabajadora',
            'totalVentasEfectivo', 
            'totalFiadoOtorgado',
            'totalFiadoCobrado',
            'dineroEsperado', 
            'detalleComprasTexto'
        ));
    }

   public function update(Request $request, $id)
    { 
        $request->validate([
            'total_efectivo_real' => 'required',
            'pago_trabajadora' => 'nullable',
            'sueldo' => 'nullable', 
            'productos' => 'required|array',
            'reporte_descuadre_cierre' => 'required|string',
            'notas_cajero' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $turno = Turno::with(['compras', 'ventas', 'detalles', 'cobrosFiados'])->findOrFail($id);

            foreach ($request->productos as $prod) {
                $detalle = TurnoDetalle::where('turno_id', $turno->id)
                    ->where('producto_id', $prod['id'])
                    ->first();

                if ($detalle) {
                    $comprasDelProducto = 0;
                    if ($turno->compras) {
                        foreach ($turno->compras as $compra) {
                            if ($compra->detalles) {
                                $comprasDelProducto += $compra->detalles->where('producto_id', $prod['id'])->sum('cantidad');
                            }
                        }
                    }

                    $ventasDelProducto = 0;
                    if ($turno->ventas) {
                        foreach ($turno->ventas as $venta) {
                            if ($venta->detalles) {
                                $ventasDelProducto += $venta->detalles->where('producto_id', $prod['id'])->sum('cantidad');
                            }
                        }
                    }

                    $stockEsperadoCierre = ($detalle->stock_fisico_apertura + $comprasDelProducto) - $ventasDelProducto;
                    // Limpiar stock por si viene con formato extraño
                    $stockFisicoCierre = (int) str_replace(['.', ','], ['', ''], $prod['stock_fisico']);
                    $diferenciaCierre = $stockFisicoCierre - $stockEsperadoCierre;

                    $detalle->update([
                        'stock_sistema_cierre' => $stockEsperadoCierre,
                        'stock_fisico_cierre' => $stockFisicoCierre,
                        'diferencia_cierre' => $diferenciaCierre,
                    ]);
                }
            }

            // LIMPIEZA DE FORMATOS MONEDA (Elimina puntos de miles colombianos y cambia coma decimal si la hay)
            $rawEfectivoReal = $request->input('total_efectivo_real', 0);
            $efectivoReal = (float) str_replace(['.', ','], ['', '.'], is_numeric($rawEfectivoReal) ? $rawEfectivoReal : str_replace(['$', ' '], '', $rawEfectivoReal));

            $rawSueldo = $request->input('pago_trabajadora') ?? $request->input('sueldo', 0);
            $pagoTrabajadora = (float) str_replace(['.', ','], ['', '.'], is_numeric($rawSueldo) ? $rawSueldo : str_replace(['$', ' '], '', $rawSueldo));

            // Totales de compras y ventas
            $totalCompras = $turno->compras ? $turno->compras->sum('total') : 0;
            $totalVentasEfectivo = $turno->ventas ? $turno->ventas->where('metodo_pago', '!=', 'fiado')->whereNull('cliente_fiado')->sum('pago_efectivo') : 0;

            // Fiados cobrados en este turno
            $cobrosFiadosNuevos = $turno->cobrosFiados ?? collect();
            $cobrosFiadosAntiguos = $turno->ventas ? $turno->ventas->where('estado_pago', 'pagado')->whereNotNull('cliente_fiado')->whereNull('turno_pago_id') : collect();
            $cobrosFiados = $cobrosFiadosNuevos->merge($cobrosFiadosAntiguos)->unique('id');
            $totalFiadoCobrado = $cobrosFiados->sum('pago_efectivo');

            // Cálculo matemático oficial en el servidor
            $dineroEsperado = ($turno->base_caja + $totalVentasEfectivo + $totalFiadoCobrado) - $totalCompras - $pagoTrabajadora;
            
            // Diferencia real: Lo que hay físicamente en caja menos lo que matemáticamente debería haber
            $diferenciaEfectivo = $efectivoReal - $dineroEsperado;

            $notasAperturaOriginales =  $turno->notas;
            $notasFinales = $request->reporte_descuadre_cierre;
            if ($request->filled('notas_cajero')) {
                $notasFinales = "Notas del cajero: " . $request->notas_cajero . "\n\n" . $notasFinales;
            }
            if (!empty($notasAperturaOriginales)) { 

            $notasFinales = "--- NOTAS DE APERTURA ---\n". $notasAperturaOriginales. "\n\n--- CIERRE DE TURNO ---\n" . $notasFinales;

              }

            // Estado basado en la diferencia (tolerancia menor a 1 centavo)
            $estadoTurno = abs($diferenciaEfectivo) < 0.01 ? 'cerrado_ok' : 'cerrado_descuadre';

            // Guardar en base de datos con los valores limpios y reales
            $turno->update([
                'fecha_cierre' => now(),
                'total_efectivo_esperado' => $dineroEsperado,  
                'total_efectivo_real' => $efectivoReal,
                'total_descuadre_dinero' => $diferenciaEfectivo, 
                'estado' => $estadoTurno,                      
                'sueldo' => $pagoTrabajadora,                  
                'notas' => $notasFinales,
            ]);

            DB::commit();

            return redirect()->route('turnos.index')
                ->with('mensaje', 'Turno cerrado con éxito.')
                ->with('icono', 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('mensaje', 'Ocurrió un error al cerrar el turno: ' . $e->getMessage())
                ->with('icono', 'error');
        }
    }
    public function registrarVentaOlvidada(Request $request)
    {
        $request->validate([
            'turno_id' => 'required|exists:turnos,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $turno = Turno::findOrFail($request->turno_id);
            $producto = Producto::findOrFail($request->producto_id);
            $subtotal = $producto->precio_venta * $request->cantidad;

            $venta = new Venta();
            $venta->turno_id = $turno->id;
            $venta->user_id = Auth::id();
            $venta->metodo_pago = 'efectivo';
            $venta->estado_pago = 'pagado';
            $venta->pago_efectivo = $subtotal;
            $venta->total = $subtotal;
            $venta->observaciones = $request->motivo ?? 'Venta olvidada registrada durante el cierre de turno';
            $venta->save();

            VentaDetalle::create([
                'venta_id' => $venta->id,
                'producto_id' => $producto->id,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $producto->precio_venta,
                'subtotal' => $subtotal,
            ]);

            $producto->decrement('stock', $request->cantidad);

            $detalleTurno = TurnoDetalle::where('turno_id', $turno->id)
                ->where('producto_id', $producto->id)
                ->first();

            $nuevoStockEsperado = 0;
            if ($detalleTurno) {
                $comprasProd = 0;
                if ($turno->compras) {
                    foreach ($turno->compras as $compra) {
                        if ($compra->detalles) {
                            $comprasProd += $compra->detalles->where('producto_id', $producto->id)->sum('cantidad');
                        }
                    }
                }

                $ventasProd = 0;
                $ventasActualizadas = Venta::where('turno_id', $turno->id)->with('detalles')->get();
                foreach ($ventasActualizadas as $v) {
                    if ($v->detalles) {
                        $ventasProd += $v->detalles->where('producto_id', $producto->id)->sum('cantidad');
                    }
                }

                $nuevoStockEsperado = ($detalleTurno->stock_fisico_apertura + $comprasProd) - $ventasProd;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta olvidada registrada y dinero/stock actualizados.',
                'subtotal' => $subtotal,
                'producto_id' => $producto->id,
                'producto_nombre' => $producto->nombre,
                'cantidad' => $request->cantidad,
                'nuevo_stock_esperado' => $nuevoStockEsperado
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la venta olvidada: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {

        try {
            DB::beginTransaction();

            $turno = Turno::with([
                'ventas.detalles', 
                'compras.detalles', 
                'cobrosFiados', 
                'detalles'
            ])->findOrFail($id);

            // 2. Revertir el stock de las ventas realizadas en este turno
            if ($turno->ventas) {
                foreach ($turno->ventas as $venta) {
                    if ($venta->detalles) {
                        foreach ($venta->detalles as $det) {
                            $prod = Producto::find($det->producto_id);
                            if ($prod) {
                                $prod->increment('stock', $det->cantidad);
                            }
                        }
                    }
                    $venta->detalles()->delete();
                }
                $turno->ventas()->delete();
            }

            // 3. Revertir el stock de las compras realizadas en este turno
            if ($turno->compras) {
                foreach ($turno->compras as $compra) {
                    if ($compra->detalles) {
                        foreach ($compra->detalles as $detCompra) {
                            $prod = Producto::find($detCompra->producto_id);
                            if ($prod) {
                                $prod->decrement('stock', $detCompra->cantidad);
                            }
                        }
                    }
                    $compra->detalles()->delete();
                }
                $turno->compras()->delete();
            }

            // 4. Si en este turno se cobraron fiados de otros turnos, volverlos a estado pendiente
            if ($turno->cobrosFiados) {
                foreach ($turno->cobrosFiados as $fiadoCobrado) {
                    $fiadoCobrado->update([
                        'turno_pago_id' => null,
                        'estado_pago' => 'pendiente',
                        'fecha_pago' => null,
                        'metodo_pago_saldo' => null,
                        'pago_efectivo' => 0,
                        'pago_transferencia' => 0,
                    ]);
                }
            }

            // 5. Eliminar detalles del conteo de apertura/cierre
            $turno->detalles()->delete();

            // 6. Eliminar el turno
            $turno->delete();

            DB::commit();

            return redirect()->route('turnos.index')
                ->with('mensaje', 'Turno #' . $id . ' eliminado exitosamente y stock sincronizado.')
                ->with('icono', 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('turnos.index')
                ->with('mensaje', 'Ocurrió un error al intentar eliminar el turno: ' . $e->getMessage())
                ->with('icono', 'error');
        }
    }
}