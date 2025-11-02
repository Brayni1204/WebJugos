<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Componente extends Model
{
    use HasFactory;

    protected $fillable = ['id_producto', 'nombre_componente', 'cantidad', 'status'];


    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
