<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'turno_id',
        'turno_pago_id',
        'total',
        'metodo_pago',
        'tipo_pago',
        'pago_efectivo',
        'pago_transferencia',
        'aplica_promocion',
        'cliente_fiado',
        'estado_pago',
        'fecha_pago',
        'metodo_pago_saldo',
        'observaciones', // <-- Agregado para permitir asignación masiva
    ];

    // Relación con el usuario/cajero que hizo la venta
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el turno en el que se realizó la venta (donde salieron los productos)
    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    // Relación con el turno en el que se cobró la venta fiada (donde ingresó el dinero)
    public function turnoPago()
    {
        return $this->belongsTo(Turno::class, 'turno_pago_id');
    }

    // Relación con los productos/detalles de la venta
    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }
}