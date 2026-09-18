<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        return view('admin.ventas.create', compact('categorias', 'productos', 'turnoActivo'));
    }

    /**
     * Almacena una nueva venta procesada desde el POS.
     */
    public function store(Request $request)
    {
        $request->validate([
            'metodo_pago'        => 'required|in:efectivo,transferencia,mixto,fiado',
            'cliente_fiado'     => 'required_if:metodo_pago,fiado|nullable|string|max:255',
            'pago_efectivo'      => 'nullable|numeric|min:0',
            'pago_transferencia' => 'nullable|numeric|min:0',
            'aplica_promocion'   => 'nullable',
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
                $producto = Producto::with('categoria')->findOrFail($item['id']);

                if ($producto->stock < $item['cantidad']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "El producto '{$producto->nombre}' no tiene suficiente stock (Disponible: {$producto->stock})."
                    ], 400);
                }

                $subtotal = $producto->precio_venta * $item['cantidad'];

                $nombreCategoria = strtolower($producto->categoria->nombre ?? '');
                if ($aplicaPromo && str_contains($nombreCategoria, 'coctel') && $item['cantidad'] >= 2) {
                    $parejas = floor($item['cantidad'] / 2);
                    $descuento = $parejas * 4000;
                    $subtotal -= $descuento;
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
                // CORRECCIÓN: El ingreso real a caja es estrictamente el total de la venta, 
                // sin importar si el cliente pagó con un billete mayor (el cambio se devuelve físicamente).
                $pagoEfectivo = $totalVenta;
            } else if ($request->metodo_pago === 'transferencia') {
                $pagoTransferencia = $request->pago_transferencia ?? $totalVenta;
            } else if ($request->metodo_pago === 'mixto') {
                // En pago mixto, si especifican cuánto fue en efectivo para la cuenta, respetamos ese monto 
                // (o el total restante si aplica), asegurando que no exceda el total de la venta.
                $efectivoIngresado = $request->pago_efectivo ?? 0;
                $pagoEfectivo = min($efectivoIngresado, $totalVenta);
                $pagoTransferencia = max(0, $totalVenta - $pagoEfectivo);
            }

            // Registrar cabecera de la venta
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

            // Registrar detalles de los productos y actualizar el stock
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

            // Registro automático de novedad en el turno si la venta fue fiada
            if ($request->metodo_pago === 'fiado') {
                $prodsStr = implode(', ', $resumenProductosTexto);
                $notaFiado = "\n- [VENTA FIADA] Cliente: {$clienteFiado} | Total: $" . number_format($totalVenta, 0, ',', '.') . " | Productos entregados: {$prodsStr} (Salida de inventario sin ingreso de dinero en caja).";
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
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar la venta: ' . $e->getMessage()
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

            // Preservar el turno_id original (para que el inventario del turno de origen no se descuadre)
            // y registrar el turno_pago_id para que el dinero ingrese a la caja del turno activo
            $venta->turno_pago_id    = $turnoActivo->id;
            $venta->fecha_pago       = \Carbon\Carbon::now();
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

            // Registro automático en las notas del turno al recibir el pago
            $clienteNombre = $venta->cliente_fiado ?? 'Cliente';
            $metodoTexto = strtoupper($metodo);
            $notaPagoFiado = "\n- [INGRESO POR COBRO DE FIADO] Cliente: {$clienteNombre} | Monto abonado: $" . number_format($monto, 0, ',', '.') . " ({$metodoTexto}) | Dinero ingresado a caja en turno activo.";
            
            $turnoActivo->notas .= $notaPagoFiado;
            $turnoActivo->save();

            DB::commit();

            return redirect()->back()
                ->with('mensaje', '¡Deuda saldada e ingresada a la caja del turno actual correctamente!')
                ->with('icono', 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('mensaje', 'Error al procesar el pago: ' . $e->getMessage())
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
     * Elimina una venta y reestablece las cantidades al inventario (Stock).
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $venta = Venta::with(['detalles', 'turno'])->findOrFail($id);

            // Blindaje: No permitir eliminar ventas de turnos que ya fueron cerrados
            if ($venta->turno && $venta->turno->estado !== 'abierto') {
                return redirect()->route('ventas.index')
                    ->with('mensaje', 'No puedes eliminar una venta de un turno que ya ha sido cerrado para proteger el historial contable.')
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
            return redirect()->route('ventas.index')
                ->with('mensaje', 'Error al eliminar la venta: ' . $e->getMessage())
                ->with('icono', 'error');
        }
    }
}