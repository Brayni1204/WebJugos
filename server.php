<?php
// server.php (Versión corregida y robusta)

error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();

$host = "0.0.0.0";
$port = 8090;

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socket, $host, $port);
socket_listen($socket);

$clients = [$socket];

echo "✅ Servidor WebSocket iniciado en ws://127.0.0.1:$port\n";

while (true) {
    $read = $clients;
    $write = $except = null;
    socket_select($read, $write, $except, null);

    foreach ($read as $client) {
        if ($client === $socket) {
            $newClient = socket_accept($socket);
            if ($newClient) {
                $clients[] = $newClient;
                handshake($newClient);
                echo "🟢 Cliente conectado.\n";
            }
        } else {
            $data = @socket_read($client, 4096);

            if ($data === false || empty($data)) {
                echo "🔴 Cliente desconectado.\n";
                $clientIndex = array_search($client, $clients);
                unset($clients[$clientIndex]);
                socket_close($client);
                continue;
            }

            $messagePayload = unmask($data);
            if ($messagePayload === false) continue; // Si el frame no es válido, lo ignoramos

            echo "📩 Mensaje recibido: $messagePayload\n";

            // Re-empaquetamos el mensaje limpio para todos los navegadores
            $broadcastMessage = mask($messagePayload);

            // Enviamos el mensaje a todos los clientes conectados
            foreach ($clients as $sendClient) {
                if ($sendClient !== $socket) {
                    @socket_write($sendClient, $broadcastMessage, strlen($broadcastMessage));
                }
            }
        }
    }
}


// --- FUNCIONES AUXILIARES MEJORADAS ---

function handshake($client)
{
    $headers = socket_read($client, 2048);
    if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $headers, $matches)) {
        $key = trim($matches[1]);
        $acceptKey = base64_encode(pack('H*', sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        $response = "HTTP/1.1 101 Switching Protocols\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "Sec-WebSocket-Accept: $acceptKey\r\n\r\n";
        socket_write($client, $response, strlen($response));
    }
}

/**
 * ✅ FUNCIÓN UNMASK CORREGIDA Y ROBUSTA
 * Desempaca el mensaje del cliente, validando el tamaño correcto.
 */
function unmask($payload)
{
    if (strlen($payload) < 2) {
        return false;
    }
    $length = ord($payload[1]) & 127;
    $mask_offset = 2;
    if ($length == 126) {
        if (strlen($payload) < 4) return false;
        $length = unpack('n', substr($payload, 2, 2))[1];
        $mask_offset = 4;
    } elseif ($length == 127) {
        if (strlen($payload) < 10) return false;
        $length = unpack('J', substr($payload, 2, 8))[1];
        $mask_offset = 10;
    }

    if (strlen($payload) < $mask_offset + 4) return false;
    $masks = substr($payload, $mask_offset, 4);
    $data_offset = $mask_offset + 4;

    if (strlen($payload) < $data_offset + $length) return false;
    $data = substr($payload, $data_offset, $length);

    $text = "";
    for ($i = 0; $i < strlen($data); ++$i) {
        $text .= $data[$i] ^ $masks[$i % 4];
    }
    return $text;
}


/**
 * ✅ FUNCIÓN MASK CORREGIDA Y ROBUSTA
 * Empaca el mensaje para el navegador, manejando correctamente UTF-8.
 */
function mask($text)
{
    $b1 = 0x81; // opcode para texto
    $length = mb_strlen($text, 'UTF-8');

    if ($length <= 125) {
        $header = pack('CC', $b1, $length);
    } elseif ($length > 125 && $length < 65536) {
        $header = pack('CCn', $b1, 126, $length);
    } elseif ($length >= 65536) {
        $header = pack('CCNN', $b1, 127, 0, $length);
    }
    return $header . $text;
}
