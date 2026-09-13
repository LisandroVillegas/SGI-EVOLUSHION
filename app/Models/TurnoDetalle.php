<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TurnoDetalle extends Model
{
    use HasFactory;

    protected $fillable = [
        'turno_id',
        'producto_id',
        'stock_sistema_apertura',
        'stock_fisico_apertura',
        'diferencia_apertura',
        'stock_sistema_cierre',
        'stock_fisico_cierre',
        'diferencia_cierre',
        'observacion',
    ];

    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}