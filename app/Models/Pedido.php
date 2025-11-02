<?php

namespace App\Models;

use App\Events\PedidoActualizado;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $dispatchesEvents = [
        'created' => PedidoActualizado::class,
        'updated' => PedidoActualizado::class,
        'deleted' => PedidoActualizado::class,
    ];
    protected $fillable = [
        'mesa_id',
        'metodo_entrega',
        'metodo_pago',
        'id_user',
        'cliente_id',
        'subtotal',
        'costo_delivery',
        'total_pago',
        'estado'
    ];
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }
    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }
    public function direccion()
    {
        return $this->hasOne(Direccion::class, 'pedido_id');
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function estadoPedidos()
    {
        return $this->hasMany(EstadoPedido::class, 'pedido_id'); 
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'pedido_id');
    }

    public function actualizarEstado()
    {
        $estados = ['En local', 'En camino', 'En tu Dirección', 'Entregado'];
        $estadoActual = $this->estadoPedidos->last()->estado ?? 'En local';
        $indiceActual = array_search($estadoActual, $estados);
        if ($indiceActual !== false && isset($estados[$indiceActual + 1])) {
            $nuevoEstado = $estados[$indiceActual + 1];
            EstadoPedido::create([
                'pedido_id' => $this->id,
                'estado' => $nuevoEstado,
            ]);
            return true;
        }
        return false;
    }
}
