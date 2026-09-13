<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fecha_inicio',
        'fecha_cierre',
        'base_caja',
        'total_efectivo_esperado',
        'total_efectivo_real',
        'total_descuadre_dinero',
        'estado',
        'notas',
        'sueldo',
    ];

    // Relación con el usuario/cajero
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con los detalles del conteo de inventario
    public function detalles()
    {
        return $this->hasMany(TurnoDetalle::class);
    }

    // Relación con las compras de productos hechas en el turno
    public function compras()
    {
        return $this->hasMany(Compra::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}