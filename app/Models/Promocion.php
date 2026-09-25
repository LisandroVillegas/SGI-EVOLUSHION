<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    use HasFactory;

    // Indicamos el nombre exacto de la tabla en español
    protected $table = 'promociones';

    protected $fillable = [
        'nombre',
        'categoria_id',
        'producto_id',
        'cantidad_minima',
        'descuento',
        'estado',
    ];

    /**
     * Relación: Una promoción pertenece a una categoría.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}