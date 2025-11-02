<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user', // 🔹 Agrega esta línea
        'pedido_id',
        'cliente_id',
        'subtotal',
        'costo_delivery',
        'total_pago',
        'estado'
    ];

    // Relación con Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Relación con Usuario (quien registró la venta)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relación con Pedido (si proviene de un pedido)
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    // Relación con DetalleVenta
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }
}
