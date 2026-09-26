<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ConfiguracionController extends Controller
{
    // Muestra la vista donde se cambia el PIN
    public function index()
    {
        return view('admin.configuraciones.index');
    }

    // Procesa el cambio de PIN
    public function updatePin(Request $request)
    {
        $request->validate([
            'pin_actual' => 'required',
            'pin_nuevo' => 'required|numeric|digits:4',
        ]);

        $pinBD = DB::table('configuraciones')->where('clave', 'pin_seguridad')->value('valor') ?? env('PIN_TURNO_ELIMINAR', '1234');

        $coincide = false;
        if (Hash::needsRehash($pinBD)) {
            // Si el PIN actual en base de datos está en texto plano
            $coincide = hash_equals((string)$pinBD, (string)$request->pin_actual);
        } else {
            // Si el PIN actual ya está hasheado
            $coincide = Hash::check($request->pin_actual, $pinBD);
        }

        if (!$coincide) {
            return redirect()->back()
                ->with('mensaje', 'El PIN actual no coincide.')
                ->with('icono', 'error');
        }

        DB::table('configuraciones')->updateOrInsert(
            ['clave' => 'pin_seguridad'],
            ['valor' => Hash::make($request->pin_nuevo), 'updated_at' => now()]
        );

        return redirect()->back()
            ->with('mensaje', 'PIN de seguridad actualizado y protegido correctamente.')
            ->with('icono', 'success');
    }
}