<?php
include "conexion_db.php"; // Contiene $host, $user, $pw, $db

// --- Configurar cabecera para salida JSON ---
header("Content-Type: application/json; charset=UTF-8");

// --- Conexión segura a la base de datos ---
$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión a la base de datos"]);
    exit;
}

// --- Validar parámetro colmena_id ---
if (empty($_GET['colmena_id']) || !ctype_digit($_GET['colmena_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Falta o es inválido el parámetro colmena_id"]);
    exit;
}

$colmena_id = (int) $_GET['colmena_id'];

// --- Obtener datos de la tabla control_colmena ---
$stmt_control = $mysqli->prepare("
    SELECT modo
    FROM control_colmena
    WHERE colmena_id = ?
    LIMIT 1
");
$stmt_control->bind_param("i", $colmena_id);
$stmt_control->execute();
$result_control = $stmt_control->get_result();

// --- Verificar si se encontró el registro ---
if ($result_control->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["error" => "No se encontró información de control para la colmena"]);
    exit;
}

$control = $result_control->fetch_assoc();

// --- Obtener datos de la tabla actuadores ---
$stmt_act = $mysqli->prepare("
    SELECT nombre, estado, tipo_estado
    FROM actuadores
    WHERE colmena_id = ?
");
$stmt_act->bind_param("i", $colmena_id);
$stmt_act->execute();
$result_act = $stmt_act->get_result();

$actuadores = [];
while ($row = $result_act->fetch_assoc()) {
    $actuadores[] = [
        "nombre" => $row["nombre"],
        "estado" => (float) $row["estado"],
        "tipo_estado" => $row["tipo_estado"]
    ];
}

// --- Construir respuesta ---
$response = [
    "status" => "success",
    "colmena_id" => $colmena_id,
    "control" => [
        "modo" => $control["modo"]
    ],
    "actuadores" => $actuadores
];

// --- Enviar salida JSON ---
http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
