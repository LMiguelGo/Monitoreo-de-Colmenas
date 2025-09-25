<?php
include "conexion.php";  // Contiene $host, $user, $pw, $db

$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// --- Recibir ID de colmena desde la ESP32 ---
if (!isset($_GET['colmena_id'])) {
    die("Error: falta colmena_id");
}
$colmena_id = intval($_GET['colmena_id']); // Sanitizar

// --- Query para obtener chat_id y bot_token del apicultor propietario ---
$sql = "
    SELECT a.chat_id, a.bot_token
    FROM apicultores a
    INNER JOIN colmenas c ON a.id = c.apicultor_id
    WHERE c.id = $colmena_id
    LIMIT 1
";

$res = $mysqli->query($sql);
if (!$res || $res->num_rows === 0) {
    die("Error: colmena no encontrada o sin apicultor");
}

$row = $res->fetch_assoc();
$chat_id = $row['chat_id'];
$bot_token = $row['bot_token'];

echo $chat_id . "," . $bot_token;
?>
