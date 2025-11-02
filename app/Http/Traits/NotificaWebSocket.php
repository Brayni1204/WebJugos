<?php
// En app/Http/Traits/NotificaWebSocket.php

namespace App\Http\Traits;

use Ratchet\Client\Connector;
use React\EventLoop\Factory;

trait NotificaWebSocket
{
    public function enviarNotificacion($accion, $mensaje)
    {
        try {
            $loop = Factory::create();
            $connector = new Connector($loop);

            $payload = json_encode(['action' => $accion, 'message' => $mensaje]);

            $connector('ws://127.0.0.1:8090')->then(function ($conn) use ($payload) {
                $conn->send($payload);
                $conn->close();
            }, function ($e) {
                // Silenciosamente falla si el servidor no está activo
            });

            $loop->run();
        } catch (\Exception $e) {
            // Silenciosamente falla
        }
    }
}
