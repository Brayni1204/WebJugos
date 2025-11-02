<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class EstadoPedido extends Model
{
    /** @use HasFactory<\Database\Factories\EstadoPedidoFactory> */
    use HasFactory;
    protected $fillable = ['pedido_id', 'estado'];

    public function pedido() 
    {
        return $this->belongsTo(Pedido::class);
    }
}
