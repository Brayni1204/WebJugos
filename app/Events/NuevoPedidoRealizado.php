<?php

namespace App\Events;

use App\Models\NuevoPedido;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast as BroadcastingShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NuevoPedidoRealizado implements BroadcastingShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pedido;

    public function __construct(NuevoPedido $pedido)
    {
        $this->pedido = $pedido;
    }

    public function broadcastOn()
    {
        return new Channel('pedidos');
    }
}
