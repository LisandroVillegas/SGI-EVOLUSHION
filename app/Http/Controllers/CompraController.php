<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index()
    {
        $compras = Compra::with('detalles.producto')->latest()->get();
        return view('admin.compras.index', compact('compras'));
    }

    public function create()
    {
        $productos = Producto::with('categoria')->get();
        return view('admin.compras.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $turnoActivo = Turno::where('user_id', Auth::id())
            ->where('estado', 'abierto')
            ->first();

        if (!$turnoActivo) {
            return redirect()->back()
                ->with('mensaje', 'No tienes un turno/caja abierto. Abre un turno para registrar compras.')
                ->with('icono', 'warning');
        }

        $request->validate([
            'fecha' => 'required|date',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_compra' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, &$turnoActivo) {
            $comprobante = $request->comprobante;
            if (empty($comprobante)) {
                $ultimoId = Compra::max('id') ?? 0;
                $comprobante = 'COMP-' . str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);
            }

            $total = 0;
            foreach ($request->productos as $p) {
                $total += $p['cantidad'] * $p['precio_compra'];
            }

            $compra = Compra::create([
                'turno_id' => $turnoActivo->id,
                'tipo' => 'normal',
                'comprobante' => $comprobante,
                'fecha' => $request->fecha,
                'total' => $total,
            ]);

            foreach ($request->productos as $p) {
                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $p['producto_id'],
                    'cantidad' => $p['cantidad'],
                    'precio_compra' => $p['precio_compra'],
                ]);

                $producto = Producto::find($p['producto_id']);
                if ($producto) {
                    $producto->increment('stock', $p['cantidad']);
                }
            }

            $hora = now()->format('H:i');
            $turnoActivo->observaciones .= "\n• [{$hora}] Compra Registrada ({$comprobante}) por $" . number_format($total, 0, ',', '.');
            $turnoActivo->save();
        });

        return redirect()->to('/admin/compras')
            ->with('mensaje', 'Compra registrada e inventario actualizado exitosamente')
            ->with('icono', 'success');
    }

    public function storeRapida(Request $request)
    {
        $turnoActivo = Turno::where('user_id', Auth::id())
            ->where('estado', 'abierto')
            ->first();

        if (!$turnoActivo) {
            return redirect()->back()
                ->with('mensaje', 'No tienes un turno/caja abierto. Abre un turno para registrar compras o egresos.')
                ->with('icono', 'warning');
        }

        $request->validate([
            'concepto' => 'required|string|max:255',
            'total' => 'required|numeric|min:0',
            'fecha' => 'required|date',
        ]);

        DB::transaction(function () use ($request, &$turnoActivo) {
            $ultimoId = Compra::max('id') ?? 0;
            $comprobante = 'COMP-' . str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);

            Compra::create([
                'turno_id' => $turnoActivo->id,
                'tipo' => 'rapida',
                'concepto' => $request->concepto,
                'comprobante' => $comprobante,
                'fecha' => $request->fecha,
                'total' => $request->total,
            ]);

            $hora = now()->format('H:i');
            $turnoActivo->observaciones .= "\n• [{$hora}] Egreso/Compra Rápida: {$request->concepto} por $" . number_format($request->total, 0, ',', '.');
            $turnoActivo->save();
        });

        return redirect()->to('/admin/compras')
            ->with('mensaje', 'Compra rápida / egreso operativo registrado exitosamente')
            ->with('icono', 'success');
    }

    public function show($id)
    {
        $compra = Compra::with('detalles.producto')->findOrFail($id);
        return view('admin.compras.show', compact('compra'));
    }

    public function destroy($id)
    {
        $compra = Compra::with(['detalles', 'turno'])->findOrFail($id);

        if ($compra->turno && $compra->turno->estado !== 'abierto') {
            return redirect()->to('/admin/compras')
                ->with('mensaje', 'No puedes eliminar una compra de un turno que ya ha sido cerrado para proteger el historial contable.')
                ->with('icono', 'warning');
        }

        DB::transaction(function () use ($compra) {
            foreach ($compra->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                if ($producto) {
                    $producto->decrement('stock', $detalle->cantidad);
                }
            }

            DetalleCompra::where('compra_id', $compra->id)->delete();
            $compra->delete();
        });

        return redirect()->to('/admin/compras')
            ->with('mensaje', 'Compra eliminada y stock revertido exitosamente')
            ->with('icono', 'success');
    }
}