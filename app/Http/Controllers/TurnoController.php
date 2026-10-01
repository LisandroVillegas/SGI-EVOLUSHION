<?php

namespace App\Http\Controllers;

use App\Models\VentaDetalle;
use App\Models\Promocion; //
use App\Models\Turno;
use App\Models\Producto;
use App\Models\TurnoDetalle;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TurnoController extends Controller
{
    public function index()
    {
        $turnos = Turno::with('user')->orderBy('id', 'desc')->get();
        return view('admin.turnos.index', compact('turnos'));
    }

    public function create()
    {
        // Validar que no exista NINGÚN turno abierto en todo el sistema
        $turnoActivo = Turno::where('estado', 'abierto')->with('user')->first();

        if ($turnoActivo) {
            $cajero = $turnoActivo->user->name ?? 'otro usuario';
            return redirect()->route('turnos.index')
                ->with('mensaje', "Ya existe una caja operando con un turno abierto (Cajero/a: {$cajero}). Solo puede haber un turno activo en el sistema.")
                ->with('icono', 'warning');
        }

        $ultimoTurnoCerrado = Turno::whereIn('estado', ['cerrado_ok', 'cerrado_descuadre'])
                                    ->orderBy('id', 'desc')
                                    ->first();

        $baseSugerida = $ultimoTurnoCerrado && $ultimoTurnoCerrado->base_siguiente_turno !== null
                        ? $ultimoTurnoCerrado->base_siguiente_turno
                        : ($ultimoTurnoCerrado->base_caja ?? 50000);

        $productos = Producto::orderBy('nombre', 'asc')->get();

        return view('admin.turnos.create', compact('productos', 'baseSugerida'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'base_caja' => 'required|numeric|min:0',
            'notas' => 'nullable|string',
            'reporte_inventario' => 'nullable|string',
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.stock_fisico' => 'required|numeric|min:0',
        ]);

        // Garantizar que no exista NINGÚN turno abierto globalmente antes de permitir abrir uno nuevo
        $turnoActivo = Turno::where('estado', 'abierto')->with('user')->first();

        if ($turnoActivo) {
            $cajero = $turnoActivo->user->name ?? 'otro usuario';
            return redirect()->route('turnos.index')
                ->with('mensaje', "No es posible abrir un nuevo turno: ya existe una caja operando con un turno abierto (Cajero/a: {$cajero}).")
                ->with('icono', 'error');
        }

        try {
            DB::beginTransaction();

            $notasApertura = $request->input('reporte_inventario', 'Apertura de turno sin novedades en inventario.');

            if ($request->filled('notas')) {
                $notasApertura .= "\n\nObservaciones Adicionales: " . $request->notas;
            }

            $turno = new Turno();
            $turno->user_id = Auth::id();
            $turno->fecha_inicio = Carbon::now();
            $turno->base_caja = $request->base_caja;
            $turno->estado = 'abierto';
            $turno->notas = $notasApertura;
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

                $producto = Producto::find($prod['id']);
                if ($producto) {
                    $producto->stock = $stockFisico;
                    $producto->save();
                }
            }

            DB::commit();

            return redirect()->route('turnos.index')
                ->with('mensaje', 'Turno abierto e inventario inicial registrado exitosamente.')
                ->with('icono', 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TurnoController@store - Error al abrir el turno: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('mensaje', 'Ocurrió un error inesperado al abrir el turno. Por favor intente nuevamente.')
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
       
        $totalVentasEfectivo = $turno->ventas ? $turno->ventas->where('metodo_pago', '!=', 'fiado')->whereNull('cliente_fiado')->sum('pago_efectivo') : 0;
        $totalVentasTransferencia = $turno->ventas ? $turno->ventas->where('metodo_pago', '!=', 'fiado')->whereNull('cliente_fiado')->sum('pago_transferencia') : 0;
       
        $ventasFiadas = $turno->ventas ? $turno->ventas->where('metodo_pago', 'fiado') : collect();
        $totalFiadoOtorgado = $ventasFiadas->sum('total');

        $cobrosFiadosNuevos = $turno->cobrosFiados ?? collect();
        $cobrosFiadosAntiguos = $turno->ventas ? $turno->ventas->where('estado_pago', 'pagado')->whereNotNull('cliente_fiado')->whereNull('turno_pago_id') : collect();
        $cobrosFiados = $cobrosFiadosNuevos->merge($cobrosFiadosAntiguos)->unique('id');
        $totalFiadoCobrado = $cobrosFiados->sum('pago_efectivo');

        $totalVentas = $turno->ventas ? $turno->ventas->sum('total') : 0;
       
        $baseSiguiente = $turno->base_siguiente_turno ?? $turno->base_caja;

        if ($turno->estado === 'abierto') {
            $pagoTrabajadora = 0;
            $dineroEsperado = ($turno->base_caja + $totalVentasEfectivo + $totalFiadoCobrado) - $totalCompras - $baseSiguiente;
            $efectivoReal = 0;
            $diferenciaCalculada = 0;
        } else {
            $pagoTrabajadora = !is_null($turno->sueldo) ? $turno->sueldo : 0;
            $dineroEsperado = !is_null($turno->total_efectivo_esperado)
                ? $turno->total_efectivo_esperado
                : (($turno->base_caja + $totalVentasEfectivo + $totalFiadoCobrado) - $totalCompras - $pagoTrabajadora - $baseSiguiente);
               
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

        if ($turno->user_id !== Auth::id()) {
            return redirect()->route('turnos.index')
                ->with('mensaje', 'No tienes permiso para modificar un turno que no te pertenece.')
                ->with('icono', 'error');
        }

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

        // Obtener Compras Rápidas / Egresos del turno
        $comprasRapidas = $turno->compras ? $turno->compras->where('tipo', 'rapida') : collect();
        $detalleEgresosTexto = [];
        foreach ($comprasRapidas as $egreso) {
            $detalleEgresosTexto[] = "- EGRESO / COMPRA RÁPIDA: " . ($egreso->concepto ?? 'Sin concepto') . " ($" . number_format($egreso->total, 0, ',', '.') . ")";
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
            'detalleComprasTexto',
            'detalleEgresosTexto'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'total_efectivo_real' => 'required',
            'pago_trabajadora' => 'nullable',
            'sueldo' => 'nullable',
            'base_siguiente_turno' => 'nullable|numeric|min:0',
            'productos' => 'required|array',
            'reporte_descuadre_cierre' => 'required|string',
            'notas_cajero' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $turno = Turno::with(['compras', 'ventas', 'detalles', 'cobrosFiados'])->findOrFail($id);

            if ($turno->user_id !== Auth::id()) {
                return redirect()->route('turnos.index')
                    ->with('mensaje', 'No tienes permiso para cerrar o modificar un turno que no te pertenece.')
                    ->with('icono', 'error');
            }

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
                    $stockFisicoCierre = (int) str_replace(['.', ','], ['', ''], $prod['stock_fisico']);
                    $diferenciaCierre = $stockFisicoCierre - $stockEsperadoCierre;

                    $detalle->update([
                        'stock_sistema_cierre' => $stockEsperadoCierre,
                        'stock_fisico_cierre' => $stockFisicoCierre,
                        'diferencia_cierre' => $diferenciaCierre,
                    ]);

                    $producto = Producto::find($prod['id']);
                    if ($producto) {
                        $producto->stock = $stockFisicoCierre;
                        $producto->save();
                    }
                }
            }

            $rawEfectivoReal = $request->input('total_efectivo_real', 0);
            $efectivoReal = (float) str_replace(['.', ','], ['', '.'], is_numeric($rawEfectivoReal) ? $rawEfectivoReal : str_replace(['$', ' '], '', $rawEfectivoReal));

            $rawSueldo = $request->input('pago_trabajadora') ?? $request->input('sueldo', 0);
            $pagoTrabajadora = (float) str_replace(['.', ','], ['', '.'], is_numeric($rawSueldo) ? $rawSueldo : str_replace(['$', ' '], '', $rawSueldo));

            $baseSiguienteTurno = (float) $request->input('base_siguiente_turno', $turno->base_caja);

            $totalCompras = $turno->compras ? $turno->compras->sum('total') : 0;
            $totalVentasEfectivo = $turno->ventas ? $turno->ventas->where('metodo_pago', '!=', 'fiado')->whereNull('cliente_fiado')->sum('pago_efectivo') : 0;

            $cobrosFiadosNuevos = $turno->cobrosFiados ?? collect();
            $cobrosFiadosAntiguos = $turno->ventas ? $turno->ventas->where('estado_pago', 'pagado')->whereNotNull('cliente_fiado')->whereNull('turno_pago_id') : collect();
            $cobrosFiados = $cobrosFiadosNuevos->merge($cobrosFiadosAntiguos)->unique('id');
            $totalFiadoCobrado = $cobrosFiados->sum('pago_efectivo');

            $dineroEsperado = ($turno->base_caja + $totalVentasEfectivo + $totalFiadoCobrado) - $totalCompras - $pagoTrabajadora - $baseSiguienteTurno;
            $diferenciaEfectivo = $efectivoReal - $dineroEsperado;

            // GENERACIÓN ESTRICTA DEL REPORTE EN EL BACKEND (Seguridad anti-manipulación)
            $notasCierre = "RESUMEN FINANCIERO DEL TURNO:\n";
            $notasCierre .= "--------------------------------\n";
            $notasCierre .= "(+) Base Inicial: $" . number_format($turno->base_caja, 0, ',', '.') . "\n";
            $notasCierre .= "(+) Ventas en Efectivo: $" . number_format($totalVentasEfectivo, 0, ',', '.') . "\n";
            if ($totalFiadoCobrado > 0) {
                $notasCierre .= "(+) Abonos a Fiados (Caja): $" . number_format($totalFiadoCobrado, 0, ',', '.') . "\n";
            }
            $notasCierre .= "(-) Compras / Egresos: $" . number_format($totalCompras, 0, ',', '.') . "\n";
            if ($pagoTrabajadora > 0) {
                $notasCierre .= "(-) Pago a Trabajadora / Sueldo: $" . number_format($pagoTrabajadora, 0, ',', '.') . "\n";
            }
            if ($baseSiguienteTurno > 0) {
                $notasCierre .= "(-) Base para el Siguiente Turno: $" . number_format($baseSiguienteTurno, 0, ',', '.') . "\n";
            }
            $notasCierre .= "--------------------------------\n";
            $notasCierre .= "(=) DINERO ESPERADO EN CAJA: $" . number_format($dineroEsperado, 0, ',', '.') . "\n";
            $notasCierre .= "(=) EFECTIVO REAL CONTADO: $" . number_format($efectivoReal, 0, ',', '.') . "\n\n";

            if ($dineroEsperado < 0) {
                $notasCierre .= "ADVERTENCIA: Los egresos y sueldos superan el efectivo disponible en caja.\n\n";
            }

            if ($diferenciaEfectivo < -0.01) {
                $notasCierre .= "- FALTANTE DE DINERO EN CAJA: -$" . number_format(abs($diferenciaEfectivo), 0, ',', '.') . "\n";
            } elseif ($diferenciaEfectivo > 0.01) {
                $notasCierre .= "- SOBRANTE DE DINERO EN CAJA: +$" . number_format($diferenciaEfectivo, 0, ',', '.') . "\n";
            } else {
                $notasCierre .= "- CAJA CUADRADA CORRECTAMENTE\n";
            }

            $notasCierre .= "\nDETALLE DE INVENTARIO Y EGRESOS:\n";
            $hayNovedades = false;

            if ($turno->compras) {
                foreach ($turno->compras->where('tipo', 'rapida') as $egreso) {
                    $notasCierre .= "- EGRESO / COMPRA RÁPIDA: " . ($egreso->concepto ?? 'Sin concepto') . " ($" . number_format($egreso->total, 0, ',', '.') . ")\n";
                    $hayNovedades = true;
                }
            }

            foreach ($request->productos as $prod) {
                $detalle = TurnoDetalle::with('producto')->where('turno_id', $turno->id)->where('producto_id', $prod['id'])->first();
                if ($detalle && isset($detalle->diferencia_cierre)) {
                    $dif = $detalle->diferencia_cierre;
                    if ($dif < 0) {
                        $notasCierre .= "- FALTANTE EN CIERRE: " . ($detalle->producto->nombre ?? 'Producto') . " (" . abs($dif) . " und)\n";
                        $hayNovedades = true;
                    } elseif ($dif > 0) {
                        $notasCierre .= "- SOBRANTE EN CIERRE: " . ($detalle->producto->nombre ?? 'Producto') . " (+" . $dif . " und)\n";
                        $hayNovedades = true;
                    }
                }
            }

            if (!$hayNovedades) {
                $notasCierre .= "- Sin novedades de inventario ni egresos.\n";
            }

            if ($request->filled('notas_cajero')) {
                $notasCierre .= "\nObservaciones del Cajero:\n" . $request->notas_cajero . "\n";
            }

            // Concatenar notas de apertura si las hay
            $notasFinales = "";
            $notasAperturaOriginales = $turno->notas;
            // Evitar duplicar "=== NOTAS / APERTURA ===" si ya estaba
            if (!empty($notasAperturaOriginales)) {
                if (strpos($notasAperturaOriginales, '=== NOTAS / APERTURA ===') === false) {
                    $notasFinales .= "=== NOTAS / APERTURA ===\n" . $notasAperturaOriginales . "\n\n";
                } else {
                    $notasFinales .= $notasAperturaOriginales . "\n\n";
                }
            }
            $notasFinales .= "=== CIERRE DE TURNO ===\n" . $notasCierre;

            $estadoTurno = abs($diferenciaEfectivo) < 0.01 ? 'cerrado_ok' : 'cerrado_descuadre';

            $turno->update([
                'fecha_cierre' => now(),
                'total_efectivo_esperado' => $dineroEsperado,  
                'total_efectivo_real' => $efectivoReal,
                'total_descuadre_dinero' => $diferenciaEfectivo,
                'estado' => $estadoTurno,                      
                'sueldo' => $pagoTrabajadora,
                'base_siguiente_turno' => $baseSiguienteTurno,                      
                'notas' => $notasFinales,
            ]);

            DB::commit();

            return redirect()->route('turnos.index')
                ->with('mensaje', 'Turno cerrado con éxito.')
                ->with('icono', 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TurnoController@update - Error al cerrar el turno: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('mensaje', 'Ocurrió un error inesperado al cerrar el turno. Por favor intente nuevamente.')
                ->with('icono', 'error');
        }
    }

    public function registrarVentaOlvidada(Request $request)
    {
        $request->validate([
            'turno_id'         => 'required|exists:turnos,id',
            'producto_id'      => 'required|exists:productos,id',
            'cantidad'         => 'required|integer|min:1|max:999',
            'metodo_pago'      => 'required|in:efectivo,transferencia',
            'motivo'           => 'nullable|string|max:255',
            'aplica_promocion' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $turno = Turno::where('id', $request->turno_id)
                          ->where('estado', 'abierto')
                          ->where('user_id', Auth::id())
                          ->first();

            if (!$turno) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes registrar ventas en un turno cerrado o que no te pertenece.'
                ], 403);
            }

            $producto = Producto::with('categoria')->lockForUpdate()->findOrFail($request->producto_id);

            if ($producto->stock < $request->cantidad) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "El producto '{$producto->nombre}' no tiene suficiente stock disponible (Disponible: {$producto->stock})."
                ], 400);
            }

            // Lógica de Promoción
            $aplicaPromo = filter_var($request->aplica_promocion, FILTER_VALIDATE_BOOLEAN);
            $subtotal = $producto->precio_venta * $request->cantidad;
            $descuentoTotal = 0;

            // Prioridad 1: Buscar promoción específica del producto
            $promocion = Promocion::where('producto_id', $producto->id)
                                  ->where('estado', true)
                                  ->first();

            // Prioridad 2: Buscar promoción por categoría
            if (!$promocion) {
                $promocion = Promocion::whereNull('producto_id')
                                      ->where('categoria_id', $producto->categoria_id)
                                      ->where('estado', true)
                                      ->first();
            }

            if ($aplicaPromo && $promocion && $request->cantidad >= $promocion->cantidad_minima) {
                $grupos = floor($request->cantidad / $promocion->cantidad_minima);
                $descuentoTotal = $grupos * $promocion->descuento;
                $subtotal -= $descuentoTotal;
            }

            $metodo = $request->metodo_pago;

            $venta = new Venta();
            $venta->turno_id = $turno->id;
            $venta->user_id = Auth::id();
            $venta->metodo_pago = $metodo;
            $venta->estado_pago = 'pagado';
            $venta->aplica_promocion = $aplicaPromo ? 1 : 0;
            $venta->pago_efectivo = ($metodo === 'efectivo') ? $subtotal : 0;
            $venta->pago_transferencia = ($metodo === 'transferencia') ? $subtotal : 0;
            $venta->total = $subtotal;
            $venta->observaciones = $request->motivo ?? 'Venta olvidada registrada durante el cierre de turno';
            $venta->save();

            VentaDetalle::create([
                'venta_id'        => $venta->id,
                'producto_id'     => $producto->id,
                'cantidad'        => $request->cantidad,
                'precio_unitario' => $producto->precio_venta,
                'subtotal'        => $subtotal,
            ]);

            $producto->decrement('stock', $request->cantidad);

            // Anexar automáticamente a las notas/observaciones del turno
            $detallePromo = ($aplicaPromo && $descuentoTotal > 0) ? " (Promo Aplicada: -$" . number_format($descuentoTotal, 0, ',', '.') . ")" : "";
            $motivoTexto = $request->motivo ? " | Motivo: {$request->motivo}" : "";
            $lineaObservacion = "\n- [VENTA OLVIDADA] " . $request->cantidad . "x " . $producto->nombre . 
                                $detallePromo . " | Total: $" . number_format($subtotal, 0, ',', '.') . 
                                " (" . ucfirst($metodo) . ")" . $motivoTexto;

            $turno->notas = trim(($turno->notas ?? '') . " " . $lineaObservacion);
            $turno->save();

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
                'success'              => true,
                'message'              => 'Venta olvidada registrada y dinero/stock actualizados.',
                'subtotal'             => $subtotal,
                'producto_id'          => $producto->id,
                'producto_nombre'      => $producto->nombre,
                'cantidad'             => $request->cantidad,
                'nuevo_stock_esperado' => $nuevoStockEsperado
            ]);

        } catch (\Throwable $e) { // <--- Cambiado a Throwable para capturar cualquier fallo
            DB::rollBack();
            Log::error('TurnoController@registrarVentaOlvidada - Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error en el servidor: ' . $e->getMessage()
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

            $turno->detalles()->delete();
            $turno->delete();

            DB::commit();

            return redirect()->route('turnos.index')
                ->with('mensaje', 'Turno #' . $id . ' eliminado exitosamente y stock sincronizado.')
                ->with('icono', 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TurnoController@destroy - Error al eliminar el turno #' . $id . ': ' . $e->getMessage(), [
                'user_id'  => Auth::id(),
                'turno_id' => $id,
                'trace'    => $e->getTraceAsString(),
            ]);
            return redirect()->route('turnos.index')
                ->with('mensaje', 'Ocurrió un error inesperado al eliminar el turno. Por favor intente nuevamente.')
                ->with('icono', 'error');
        }
    }
}