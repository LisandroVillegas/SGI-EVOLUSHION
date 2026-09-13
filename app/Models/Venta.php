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
        'total',
        'metodo_pago',
        'tipo_pago',
        'pago_efectivo',
        'pago_transferencia',
        'aplica_promocion',
        'cliente_fiado',
        'estado_pago',
        'observaciones', // <-- Agregado para permitir asignación masiva
    ];

    // Relación con el usuario/cajero que hizo la venta
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el turno en el que se realizó la venta
    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    // Relación con los productos/detalles de la venta
    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }
}