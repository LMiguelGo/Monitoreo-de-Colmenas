<?php
header('Content-Type: application/json; charset=utf-8');
require_once("../config/conexion_bd.php");

$id_colmena = isset($_GET['id_colmena']) ? intval($_GET['id_colmena']) : 0;
if ($id_colmena <= 0) {
  echo json_encode([]);
  exit;
}

$mysqli = new mysqli($host, $user, $pw, $db);
if ($mysqli->connect_errno) {
  http_response_code(500);
  echo json_encode(["error" => "Error de conexión"]);
  exit;
}

$sql = "SELECT temperatura, humedad, actividad_entrante, actividad_saliente,
               CONCAT(fecha, ' ', hora) AS tiempo
        FROM datos_medidos
        WHERE colmena_id = ?
        ORDER BY CONCAT(fecha, ' ', hora) DESC
        LIMIT 50";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id_colmena);
$stmt->execute();
$res = $stmt->get_result();

$datos = [];
while ($row = $res->fetch_assoc()) {
  $datos[] = [
    "tiempo" => $row["tiempo"],
    "temperatura" => floatval($row["temperatura"]),
    "humedad" => floatval($row["humedad"]),
    "actividad_entrante" => floatval($row["actividad_entrante"]),
    "actividad_saliente" => floatval($row["actividad_saliente"])
  ];
}

// Invertimos el orden para que aparezcan del más antiguo al más nuevo
$datos = array_reverse($datos);

echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
?>
