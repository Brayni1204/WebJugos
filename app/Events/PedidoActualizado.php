<?php

namespace App\Events;

use App\Models\NuevoPedido;
use App\Models\Pedido;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PedidoActualizado implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $pedido;

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    public function broadcastOn()
    {
        return new Channel('pedidos');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->pedido->id,
            'cliente' => $this->pedido->cliente->nombre,
            'fecha' => $this->pedido->created_at->format('d/m/Y H:i'),
            'total' => number_format($this->pedido->total_pago, 2),
            'estado' => $this->pedido->estado,
            'metodo_entrega' => $this->pedido->metodo_entrega
        ];
    }
}
