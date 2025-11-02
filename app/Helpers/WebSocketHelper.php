<?php

if (!function_exists('enviarWebSocket')) {
    function enviarWebSocket($mensaje)
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($socket, '127.0.0.1', 8080);
        $mensaje = mask($mensaje);
        socket_write($socket, $mensaje, strlen($mensaje));
        socket_close($socket);
    }

    function mask($text)
    {
        $b1 = 0x81;
        $length = strlen($text);
        if ($length <= 125) {
            $header = pack('CC', $b1, $length);
        } elseif ($length <= 65535) {
            $header = pack('CCn', $b1, 126, $length);
        } else {
            $header = pack('CCNN', $b1, 127, 0, $length);
        }
        return $header . $text;
    }
}
