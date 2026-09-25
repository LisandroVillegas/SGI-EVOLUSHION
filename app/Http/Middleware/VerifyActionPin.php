<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VerifyActionPin
{
    public function handle(Request $request, Closure $next): Response
    {
        $pin = $request->input('pin');
        
        // Consultar la clave en la tabla configuraciones
        $pinBD = DB::table('configuraciones')->where('clave', 'pin_seguridad')->value('valor');
        $validPin = $pinBD ?? env('PIN_TURNO_ELIMINAR', '1234');

        if ($pin !== $validPin) {
            return redirect()->back()
                ->with('mensaje', 'PIN de seguridad incorrecto.')
                ->with('icono', 'error');
        }

        return $next($request);
    }
}