<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Turno;
use App\Models\Promocion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VentaController extends Controller
{
    /**
     * Muestra el historial completo de ventas.
     */
    public function index()
    {
        $ventas = Venta::with(['user', 'detalles.producto'])
                        ->orderBy('id', 'desc')
                        ->get();

        return view('admin.ventas.index', compact('ventas'));
    }

    /**
     * Muestra el Punto de Venta (POS) para crear una venta.
     */
    public function create()
    {
        $turnoActivo = Turno::where('user_id', Auth::id())
                            ->where('estado', 'abierto')
                            ->first();

        if (!$turnoActivo) {
            return redirect()->route('turnos.index')
                ->with('mensaje', 'Debes abrir un turno para ingresar al Punto de Venta (POS).')
                ->with('icono', 'warning');
        }

        $categorias = Categoria::orderBy('nombre', 'asc')->get();
        $productos = Producto::with('categoria')->orderBy('nombre', 'asc')->get();
        $promociones = Promocion::where('estado', true)->get();

        return view('admin.ventas.create', compact('categorias', 'productos', 'turnoActivo', 'promociones'));
    }

    /**
     * Almacena una nueva venta procesada desde el POS.
     */
    public function store(Request $request)
    {
        $request->validate([
            'metodo_pago'        => 'required|in:efectivo,transferencia,fiado',
            'cliente_fiado'      => 'nullable|string|max:255',
            'pago_efectivo'      => 'nullable|numeric|min:0',
            'pago_transferencia' => 'nullable|numeric|min:0',
            'aplica_promocion'   => 'nullable',
            'observaciones'      => 'nullable|string|max:500',
            'productos'          => 'required|array|min:1',
            'productos.*.id'     => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $turnoActivo = Turno::where('user_id', Auth::id())
                            ->where('estado', 'abierto')
                            ->first();

        if (!$turnoActivo) {
            return response()->json([
                'success' => false, 
                'message' => 'No hay un turno activo para procesar la venta.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $totalVenta = 0;
            $detallesParaGuardar = [];
            $resumenProductosTexto = [];
            
            $aplicaPromo = filter_var($request->aplica_promocion, FILTER_VALIDATE_BOOLEAN);

            foreach ($request->productos as $item) {
                $producto = Producto::with('categoria')->lockForUpdate()->findOrFail($item['id']);

                if ($producto->stock < $item['cantidad']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "El producto '{$producto->nombre}' no tiene suficiente stock (Disponible: {$producto->stock})."
                    ], 400);
                }

                $subtotal = $producto->precio_venta * $item['cantidad'];

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

                if ($aplicaPromo && $promocion && $item['cantidad'] >= $promocion->cantidad_minima) {
                    $grupos = floor($item['cantidad'] / $promocion->cantidad_minima);
                    $descuentoTotal = $grupos * $promocion->descuento;
                    $subtotal -= $descuentoTotal;
                }

                $totalVenta += $subtotal;

                $detallesParaGuardar[] = [
                    'producto'        => $producto,
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $producto->precio_venta,
                    'subtotal'        => $subtotal,
                ];

                $resumenProductosTexto[] = "{$item['cantidad']}x {$producto->nombre}";
            }

            $pagoEfectivo = 0;
            $pagoTransferencia = 0;
            $estadoPago = 'pagado';
            $clienteFiado = null;

            if ($request->metodo_pago === 'fiado') {
                $estadoPago = 'pendiente';
                $clienteFiado = $request->cliente_fiado;
            } else if ($request->metodo_pago === 'efectivo') {
                $pagoEfectivo = $totalVenta;
            } else if ($request->metodo_pago === 'transferencia') {
                $pagoTransferencia = $totalVenta;
                // Guardamos el nombre limpio del remitente en la misma columna
                $clienteFiado = $request->cliente_fiado; 
            }

            $venta = Venta::create([
                'user_id'            => Auth::id(),
                'turno_id'           => $turnoActivo->id,
                'total'              => $totalVenta,
                'metodo_pago'        => $request->metodo_pago,
                'aplica_promocion'   => $aplicaPromo,
                'pago_efectivo'      => $pagoEfectivo,
                'pago_transferencia' => $pagoTransferencia,
                'cliente_fiado'      => $clienteFiado,
                'estado_pago'        => $estadoPago,
                'observaciones'      => $request->observaciones,
            ]);

            foreach ($detallesParaGuardar as $det) {
                VentaDetalle::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $det['producto']->id,
                    'cantidad'        => $det['cantidad'],
                    'precio_unitario' => $det['precio_unitario'],
                    'subtotal'        => $det['subtotal'],
                ]);

                $det['producto']->decrement('stock', $det['cantidad']);
            }

            if ($request->metodo_pago === 'fiado') {
                $prodsStr = implode(', ', $resumenProductosTexto);
                $notaFiado = "\n- [VENTA FIADA] Cliente: {$clienteFiado} | Total: $" . number_format($totalVenta, 0, ',', '.') . " | Productos: {$prodsStr}.";
                $turnoActivo->notas .= $notaFiado;
                $turnoActivo->save();
            }

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => $request->metodo_pago === 'fiado' ? 'Venta fiada registrada correctamente.' : 'Venta registrada con éxito.',
                'venta_id' => $venta->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar la venta: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al procesar la venta. Por favor intente nuevamente.'
            ], 500);
        }
    }

    /**
     * Procesa el pago de una cuenta que estaba fiada.
     */
    public function pagarFiado(Request $request, $id)
    {
        $request->validate([
            'metodo_pago_saldo' => 'required|in:efectivo,transferencia'
        ]);

        $turnoActivo = Turno::where('user_id', Auth::id())
                            ->where('estado', 'abierto')
                            ->first();

        if (!$turnoActivo) {
            return redirect()->back()
                ->with('mensaje', 'Debes tener un turno de caja abierto para recibir y registrar pagos de fiados.')
                ->with('icono', 'warning');
        }

        DB::beginTransaction();

        try {
            $venta = Venta::findOrFail($id);

            if ($venta->estado_pago === 'pagado') {
                return redirect()->back()
                    ->with('mensaje', 'Esta venta ya fue pagada previamente.')
                    ->with('icono', 'info');
            }

            $monto = $venta->total;
            $metodo = $request->metodo_pago_saldo;

            $venta->turno_pago_id    = $turnoActivo->id;
            $venta->fecha_pago       = Carbon::now();
            $venta->metodo_pago_saldo = $metodo;
            $venta->estado_pago      = 'pagado';

            if ($metodo === 'efectivo') {
                $venta->pago_efectivo = $monto;
                $venta->pago_transferencia = 0;
            } else {
                $venta->pago_transferencia = $monto;
                $venta->pago_efectivo = 0;
            }

            $venta->save();

            $clienteNombre = $venta->cliente_fiado ?? 'Cliente';
            $metodoTexto = strtoupper($metodo);
            $notaPagoFiado = "\n- [INGRESO FIADO] Cliente: {$clienteNombre} | Monto: $" . number_format($monto, 0, ',', '.') . " ({$metodoTexto}).";
            
            $turnoActivo->notas .= $notaPagoFiado;
            $turnoActivo->save();

            DB::commit();

            return redirect()->back()
                ->with('mensaje', '¡Deuda saldada e ingresada a la caja del turno actual correctamente!')
                ->with('icono', 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar el pago de fiado: ' . $e->getMessage());
            return redirect()->back()
                ->with('mensaje', 'Ocurrió un error inesperado al procesar el pago.')
                ->with('icono', 'error');
        }
    }

    /**
     * Muestra el detalle de una venta específica.
     */
    public function show($id)
    {
        $venta = Venta::with(['user', 'detalles.producto'])->findOrFail($id);
        return view('admin.ventas.show', compact('venta'));
    }

    /**
     * Genera la vista o comprobante de tique para la venta.
     */
    public function ticket($id)
    {
        $venta = Venta::with(['user', 'detalles.producto', 'turno'])->findOrFail($id);
        return view('admin.ventas.ticket', compact('venta'));
    }

    /**
     * Elimina una venta y reestablece las cantidades al inventario (Stock).
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $venta = Venta::with(['detalles', 'turno'])->findOrFail($id);

            if ($venta->turno && $venta->turno->estado !== 'abierto') {
                return redirect()->route('ventas.index')
                    ->with('mensaje', 'No puedes eliminar una venta de un turno que ya ha sido cerrado.')
                    ->with('icono', 'warning');
            }

            foreach ($venta->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                if ($producto) {
                    $producto->increment('stock', $detalle->cantidad);
                }
            }

            $venta->delete();

            DB::commit();

            return redirect()->route('ventas.index')
                ->with('mensaje', 'Venta eliminada y stock devuelto exitosamente.')
                ->with('icono', 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar la venta: ' . $e->getMessage());
            return redirect()->route('ventas.index')
                ->with('mensaje', 'Ocurrió un error inesperado al eliminar la venta.')
                ->with('icono', 'error');
        }
    }
}