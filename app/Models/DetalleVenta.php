<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'producto_id',
        'nombre_producto',
        'cantidad',
        'precio_unitario',
        'precio_total',
        'descripcion'
    ];

    // Relación con Venta
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    // Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
