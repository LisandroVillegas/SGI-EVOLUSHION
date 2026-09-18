<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyActionPin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $pin = $request->input('pin');
        $validPin = env('PIN_TURNO_ELIMINAR', '1234');

        if ($pin !== $validPin) {
            return redirect()->back()
                ->with('mensaje', 'PIN de seguridad incorrecto.')
                ->with('icono', 'error');
        }

        return $next($request);
    }
}
