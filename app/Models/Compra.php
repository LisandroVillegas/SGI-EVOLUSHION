<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'turno_id',
        'tipo',
        'concepto',
        'comprobante',
        'fecha',
        'total',
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class);
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }
}