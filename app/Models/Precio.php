<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Precio extends Model
{
    use HasFactory;
    protected $fillable = ['id_producto', 'precio_venta', 'precio_compra'];
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
