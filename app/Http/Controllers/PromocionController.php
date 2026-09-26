<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class PromocionController extends Controller
{
    /**
     * Muestra el listado de promociones.
     */
    public function index()
    {
        $promociones = Promocion::with(['categoria', 'producto'])->orderBy('id', 'desc')->get();
        return view('admin.promociones.index', compact('promociones'));
    }

    /**
     * Muestra el formulario para crear una nueva promoción.
     */
    public function create()
    {
        $categorias = Categoria::orderBy('nombre', 'asc')->get();
        $productos  = Producto::orderBy('nombre', 'asc')->get();
        
        return view('admin.promociones.create', compact('categorias', 'productos'));
    }

    /**
     * Guarda una nueva promoción en la base de datos.
     */
    public function store(Request $request)
    {
        $categoria = Categoria::find($request->categoria_id);
        $esNevera = $categoria && str_contains(strtolower($categoria->nombre), 'nevera');

        $request->validate([
            'nombre'          => 'required|string|max:255',
            'categoria_id'    => 'required|exists:categorias,id',
            'producto_id'     => $esNevera ? 'required|exists:productos,id' : 'nullable|exists:productos,id',
            'cantidad_minima' => 'required|numeric|min:1',
            'descuento'       => 'required|numeric|min:0',
        ]);

        Promocion::create([
            'nombre'          => $request->nombre,
            'categoria_id'    => $request->categoria_id,
            'producto_id'     => $esNevera ? $request->producto_id : null,
            'cantidad_minima' => $request->cantidad_minima,
            'descuento'       => $request->descuento,
            'estado'          => 1, // Siempre se crea activa por defecto
        ]);

        return redirect()->route('promociones.index')
            ->with('mensaje', 'Promoción registrada exitosamente.')
            ->with('icono', 'success');
    }

    /**
     * Muestra el formulario para editar una promoción.
     */
    public function edit($id)
    {
        $promocion  = Promocion::findOrFail($id);
        $categorias = Categoria::orderBy('nombre', 'asc')->get();
        $productos  = Producto::orderBy('nombre', 'asc')->get();

        return view('admin.promociones.edit', compact('promocion', 'categorias', 'productos'));
    }

    /**
     * Actualiza la promoción en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $promocion = Promocion::findOrFail($id);
        $categoria = Categoria::find($request->categoria_id);
        $esNevera = $categoria && str_contains(strtolower($categoria->nombre), 'nevera');

        $request->validate([
            'nombre'          => 'required|string|max:255',
            'categoria_id'    => 'required|exists:categorias,id',
            'producto_id'     => $esNevera ? 'required|exists:productos,id' : 'nullable|exists:productos,id',
            'cantidad_minima' => 'required|numeric|min:1',
            'descuento'       => 'required|numeric|min:0',
        ]);

        $promocion->update([
            'nombre'          => $request->nombre,
            'categoria_id'    => $request->categoria_id,
            'producto_id'     => $esNevera ? $request->producto_id : null,
            'cantidad_minima' => $request->cantidad_minima,
            'descuento'       => $request->descuento,
            'estado'          => 1,
        ]);

        return redirect()->route('promociones.index')
            ->with('mensaje', 'Promoción actualizada correctamente.')
            ->with('icono', 'success');
    }

    /**
     * Cambia rápidamente el estado (Activa / Inactiva) desde la tabla.
     */
    public function toggleEstado($id)
    {
        $promocion = Promocion::findOrFail($id);
        $promocion->estado = !$promocion->estado;
        $promocion->save();

        $estadoTexto = $promocion->estado ? 'activada' : 'desactivada';

        return redirect()->back()
            ->with('mensaje', "La promoción fue {$estadoTexto} correctamente.")
            ->with('icono', 'info');
    }

    /**
     * Elimina la promoción.
     */
    public function destroy($id)
    {
        $promocion = Promocion::findOrFail($id);
        $promocion->delete();

        return redirect()->route('promociones.index')
            ->with('mensaje', 'Promoción eliminada correctamente.')
            ->with('icono', 'success');
    }
}