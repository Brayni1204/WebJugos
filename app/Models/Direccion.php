<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $fillable = ['departamento', 'provincia', 'distrito', 'calle', 'numero', 'pedido_id'];

    // Relación con Pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
