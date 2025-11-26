<?php
include "conexion_db.php";  // OK

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

// --- Consulta con sentencia preparada (evita inyección SQL) ---
$stmt = $mysqli->prepare("
    SELECT temp_min, temp_max, hum_min, hum_max, activ_min, activ_max
    FROM umbrales
    WHERE colmena_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $colmena_id);
$stmt->execute();
$result = $stmt->get_result();

// --- Verificar si se encontró el registro ---
if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["error" => "No se encontraron umbrales para la colmena"]);
    exit;
}

$row = $result->fetch_assoc();

// --- Formatear los datos numéricos ---
$response = [
    "status" => "success",
    "temp_min" => (float) $row['temp_min'],
    "temp_max" => (float) $row['temp_max'],
    "hum_min"  => (float) $row['hum_min'],
    "hum_max"  => (float) $row['hum_max'],
    "activ_min" => (int) $row['activ_min'],
    "activ_max" => (int) $row['activ_max']
];

// --- Enviar salida JSON ---
http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
