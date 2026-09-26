<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class VerifyActionPin
{
    public function handle(Request $request, Closure $next): Response
    {
        $pin = (string) $request->input('pin');
        
        // Consultar la clave en la tabla configuraciones
        $pinBD = DB::table('configuraciones')->where('clave', 'pin_seguridad')->value('valor');
        $validPin = $pinBD ?? env('PIN_TURNO_ELIMINAR', '1234');

        $valido = false;
        if (Hash::needsRehash($validPin)) {
            // Compatible con PIN en texto plano existente (usando comparación en tiempo constante)
            $valido = hash_equals((string)$validPin, $pin);
        } else {
            // PIN protegido con Hash::make
            $valido = Hash::check($pin, $validPin);
        }

        if (!$valido) {
            return redirect()->back()
                ->with('mensaje', 'PIN de seguridad incorrecto.')
                ->with('icono', 'error');
        }

        return $next($request);
    }
}