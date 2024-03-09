<?php
// Consideración de seguridad: Asegúrate de que este script solo pueda ser ejecutado por usuarios autorizados.
// Puedes implementar controles de acceso, autenticación o verificar si el usuario tiene los permisos necesarios antes de ejecutar el comando.

// Consideración de rendimiento y gestión: Este enfoque es principalmente para propósitos de desarrollo o testing.
// Para producción, considera usar un gestor de procesos como systemd para un mejor control y robustez.

// Comando para ejecutar el servidor WebSocket en background
$command = 'php /var/www/html/websocket_server.php > /dev/null 2>&1 & echo $!';
$pid = shell_exec($command);

// HTML con Bootstrap para una interfaz básica
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lanzar Servidor WebSocket</title>
    <!-- Inclusión de Bootstrap CSS desde CDN para un rápido prototipado y diseño responsivo -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="mt-5">Servidor WebSocket</h1>
    <p class="alert alert-success">WebSocket server launched with PID: <?php echo $pid; ?></p>
    <p>Recuerda revisar las consideraciones importantes dentro del código y en la documentación relevante antes de usar en producción.</p>
</div>
<!-- Opcional: Inclusión de Bootstrap JS y sus dependencias (jQuery y Popper.js) para funcionalidades de componentes -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.5/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>