<?php

namespace App\Events;

use App\Models\NuevoPedido;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NuevoPedidoEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pedido;
    /**
     * Create a new event instance.
     */
    public function __construct(NuevoPedido $pedido)
    {
        $this->pedido = $pedido;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('pedidos'),
        ];
    }
    public function broadcastAs()
    {
        return 'nuevo-pedido';
    }
}
