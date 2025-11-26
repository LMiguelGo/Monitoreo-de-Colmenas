<?php
include "conexion_db.php";  // Variables: $host, $user, $pw, $db

header("Content-Type: application/json; charset=UTF-8");

// --- Validar parámetros ---
$campos = ["temperatura", "humedad", "actividad_in", "actividad_out", "ID_TARJ"];
foreach ($campos as $campo) {
    if (!isset($_GET[$campo]) || $_GET[$campo] === "") {
        http_response_code(400);
        echo json_encode(["error" => "Falta el parámetro '$campo'"]);
        exit;
    }
}

// --- Sanitizar y convertir tipos ---
$temp     = (float) $_GET["temperatura"];
$hum      = (float) $_GET["humedad"];
$act_in   = (int) $_GET["actividad_in"];
$act_out  = (int) $_GET["actividad_out"];
$id_tarj  = (int) $_GET["ID_TARJ"];

// --- Conexión con la base de datos ---
$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión a la base de datos"]);
    exit;
}

// --- Usar consulta preparada ---
$stmt = $mysqli->prepare("
    INSERT INTO datos_medidos 
        (fecha, hora, temperatura, humedad, actividad_entrante, actividad_saliente, colmena_id)
    VALUES 
        (CURDATE(), CURTIME(), ?, ?, ?, ?, ?)
");
$stmt->bind_param("ddiii", $temp, $hum, $act_in, $act_out, $id_tarj);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "mensaje" => "Datos guardados correctamente",
        "registro" => [
            "temperatura" => $temp,
            "humedad" => $hum,
            "actividad_in" => $act_in,
            "actividad_out" => $act_out,
            "colmena_id" => $id_tarj
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode([
        "error" => "Error al insertar en la base de datos",
        "detalle" => $stmt->error
    ]);
}

$stmt->close();
$mysqli->close();
?>
