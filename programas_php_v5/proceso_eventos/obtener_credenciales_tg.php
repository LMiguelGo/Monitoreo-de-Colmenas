<?php
include "conexion_db.php";  // Contiene $host, $user, $pw, $db

header('Content-Type: application/json; charset=utf-8');

$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    echo json_encode(["status" => "error", "message" => "Error de conexión: " . $mysqli->connect_error]);
    exit;
}

// --- Recibir ID de colmena desde la ESP32 ---
if (!isset($_GET['colmena_id'])) {
    echo json_encode(["status" => "error", "message" => "Falta colmena_id"]);
    exit;
}

$colmena_id = intval($_GET['colmena_id']); // Sanitizar entrada

// --- Query para obtener chat_id y bot_token del apicultor propietario ---
$sql = "
    SELECT a.chat_id, a.bot_token
    FROM apicultores a
    INNER JOIN colmenas c ON a.id = c.apicultor_id
    WHERE c.id = ?
    LIMIT 1
";

// --- Usar consulta preparada para mayor seguridad ---
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $colmena_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Colmena no encontrada o sin apicultor"]);
    exit;
}

$row = $res->fetch_assoc();

// --- Enviar respuesta en formato JSON ---
echo json_encode([
    "status" => "success",
    "chat_id" => $row['chat_id'],
    "bot_token" => $row['bot_token']
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
