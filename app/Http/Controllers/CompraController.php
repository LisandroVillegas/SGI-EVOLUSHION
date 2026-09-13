<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Turno; // <-- 1. Importamos el modelo Turno
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Importamos Auth
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
        $productos = Producto::all();
        return view('admin.compras.create', compact('productos'));
    }

    public function store(Request $request)
    {
        // 2. VERIFICACIÓN DE TURNO ABIERTO
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

        DB::transaction(function () use ($request, $turnoActivo) {
            // Autogeneración del comprobante si el usuario deja el campo en blanco
            $comprobante = $request->comprobante;
            if (empty($comprobante)) {
                $ultimoId = Compra::max('id') ?? 0;
                $comprobante = 'COMP-' . str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);
            }

            $total = 0;
            foreach ($request->productos as $p) {
                $total += $p['cantidad'] * $p['precio_compra'];
            }

            // 3. SE GUARDA EL COMPRA_ID ASOCIADO AL TURNO
            $compra = Compra::create([
                'turno_id' => $turnoActivo->id, // <-- Vinculamos la compra con el turno abierto
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
        });

        return redirect()->to('/admin/compras')
            ->with('mensaje', 'Compra registrada e inventario actualizado exitosamente')
            ->with('icono', 'success');
    }

    public function show($id)
    {
        $compra = Compra::with('detalles.producto')->findOrFail($id);
        return view('admin.compras.show', compact('compra'));
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $compra = Compra::with('detalles')->findOrFail($id);

            // Restar del stock las cantidades de los productos de esta compra
            foreach ($compra->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                if ($producto) {
                    $producto->decrement('stock', $detalle->cantidad);
                }
            }

            // Eliminar los detalles y la compra principal
            DetalleCompra::where('compra_id', $compra->id)->delete();
            $compra->delete();
        });

        return redirect()->to('/admin/compras')
            ->with('mensaje', 'Compra eliminada y stock revertido exitosamente')
            ->with('icono', 'success');
    }
}