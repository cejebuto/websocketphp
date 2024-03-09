<?php
$host = '0.0.0.0'; // Host donde se ejecutará el servidor WebSocket
$port = '8081'; // Puerto para el servidor WebSocket
$null = NULL; // Variable nula para remover clientes

// Crea el socket TCP/IP
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);

// Vincula el socket al host y puerto
socket_bind($socket, $host, $port);

// Escucha conexiones
socket_listen($socket);

// Crea y agrega la escucha del socket al listado de clientes
$clients = array($socket);

// Inicia el bucle infinito, para que el servidor escuche las conexiones entrantes
while (true) {
    $changed = $clients;
    socket_select($changed, $null, $null, 0, 10);

    if (in_array($socket, $changed)) {
        $socket_new = socket_accept($socket); // Acepta nuevas conexiones
        $clients[] = $socket_new; // Agrega el socket del cliente nuevo a la lista de clientes

        $header = socket_read($socket_new, 1024); // Lee la solicitud de actualización del WebSocket
        // Realiza el handshake
        perform_handshaking($header, $socket_new, $host, $port); // Realiza el handshake

        socket_getpeername($socket_new, $ip); // Obtiene la dirección IP del cliente
        $response = mask(json_encode(array('type'=>'system', 'message'=>$ip.' connected')));
        // y para mensajes de usuario, asegúrate de que se envíen como:
        //$response_text = mask(json_encode(array('type'=>'user', 'message'=>$valorActual)));

        send_message($response); // Envía el mensaje de conexión a todos los clientes

        $found_socket = array_search($socket, $changed); // Busca el socket en la lista de sockets cambiados
        unset($changed[$found_socket]); // Elimina el socket del array de sockets cambiados
    }

    // Maneja los mensajes entrantes de los clientes
    foreach ($changed as $changed_socket) {
            while(socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
                $received_text = unmask($buf); // Desenmascara el mensaje
                $data = json_decode($received_text, true); // Decodifica el mensaje JSON como array asociativo

                // Verifica si $data es null o si la clave 'message' no está presente
                if ($data === null || !isset($data['message'])) {
                    // Puedes decidir cómo manejar este caso: ignorar, registrar un error, etc.
                    continue; // Por ejemplo, simplemente continúa con el siguiente mensaje/cliente
                }

                // Ahora puedes acceder a $data['message'] con seguridad
                $response_text = mask(json_encode(array('type'=>'user', 'message'=>$data['message'])));
                send_message($response_text); // Envía el mensaje a todos los clientes
                break 2;
            }
    }
}


// Cierra la conexión
socket_close($socket);

function send_message($msg) {
    global $clients;
    foreach($clients as $changed_socket) {
        @socket_write($changed_socket,$msg,strlen($msg));
    }
    return true;
}

function perform_handshaking($received_header, $client_conn, $host, $port)
{
    $headers = array();
    $lines = preg_split("/\r\n/", $received_header);
    foreach ($lines as $line) {
        $line = chop($line);
        if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
            $headers[$matches[1]] = $matches[2];
        }
    }

    $secKey = $headers['Sec-WebSocket-Key'];
    $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
    // Crea las cabeceras de respuesta del handshake incluyendo Access-Control-Allow-Origin
    $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
        "Upgrade: websocket\r\n" .
        "Connection: Upgrade\r\n" .
        "Access-Control-Allow-Origin: *\r\n" .  // Permitir cualquier origen
        "Sec-WebSocket-Accept:$secAccept\r\n\r\n";

    socket_write($client_conn, $upgrade, strlen($upgrade));
}

function mask($text) {
    $b1 = 0x80 | (0x1 & 0x0f); // 0x1 texto enmarcado (frame)
    $length = strlen($text);

    if($length <= 125)
        $header = pack('CC', $b1, $length);
    elseif($length > 125 && $length < 65536)
        $header = pack('CCn', $b1, 126, $length);
    elseif($length >= 65536)
        $header = pack('CCNN', $b1, 127, $length);
    return $header.$text;
}

function unmask($payload) {
    $length = ord($payload[1]) & 127;
    if($length == 126) {
        $masks = substr($payload, 4, 4);
        $data = substr($payload, 8);
    } elseif($length == 127) {
        $masks = substr($payload, 10, 4);
        $data = substr($payload, 14);
    } else {
        $masks = substr($payload, 2, 4);
        $data = substr($payload, 6);
    }
    $text = '';
    for ($i = 0; $i < strlen($data); ++$i) {
        $text .= $data[$i] ^ $masks[$i % 4];
    }
    return $text;
}


// Funciones de enmascaramiento, desenmascaramiento
?>
