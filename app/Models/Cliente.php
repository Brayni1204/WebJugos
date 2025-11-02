<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'apellidos', 'email', 'telefono'];

    public function ventas_cliente()
    {
        return $this->hasMany(Venta::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
