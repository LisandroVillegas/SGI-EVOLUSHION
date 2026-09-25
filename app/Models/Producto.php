<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'categoria_id',
        'codigo',
        'nombre',
        'descripcion',
        'stock',
        'precio_venta',
        
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class)->withTrashed();
    }
}