<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfiguracionController extends Controller
{
    // Muestra la vista donde se cambia el PIN
    public function index()
    {
        $pinActual = DB::table('configuraciones')->where('clave', 'pin_seguridad')->value('valor') ?? '1234';
        return view('admin.configuraciones.index', compact('pinActual'));
    }

    // Procesa el cambio de PIN
    public function updatePin(Request $request)
    {
        $request->validate([
            'pin_actual' => 'required',
            'pin_nuevo' => 'required|numeric|digits:4',
        ]);

        $pinBD = DB::table('configuraciones')->where('clave', 'pin_seguridad')->value('valor') ?? '1234';

        if ($request->pin_actual !== $pinBD) {
            return redirect()->back()
                ->with('mensaje', 'El PIN actual no coincide.')
                ->with('icono', 'error');
        }

        DB::table('configuraciones')->updateOrInsert(
            ['clave' => 'pin_seguridad'],
            ['valor' => $request->pin_nuevo, 'updated_at' => now()]
        );

        return redirect()->back()
            ->with('mensaje', 'PIN de seguridad actualizado correctamente.')
            ->with('icono', 'success');
    }
}