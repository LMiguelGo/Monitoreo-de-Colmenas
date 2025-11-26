<?php
header('Content-Type: application/json; charset=utf-8');

require_once("../config/conexion_bd.php");

// Parámetros
$id_colmena = isset($_GET["id_colmena"]) ? intval($_GET["id_colmena"]) : 0;
$start      = isset($_GET["start"]) ? $_GET["start"] : date("Y-m-d H:i:s", strtotime("-1 hour"));
$periodo    = isset($_GET["periodo"]) ? $_GET["periodo"] : "5";

// Conexión
$conexion = new mysqli($host, $user, $pw, $db);
if ($conexion->connect_error) {
  http_response_code(500);
  echo json_encode(["error" => "Error de conexión"]);
  exit;
}

// Convertir el periodo a segundos
$segundos = 5; // valor por defecto
switch ($periodo) {
  case "10": $segundos = 10; break;
  case "20": $segundos = 20; break;
  case "30": $segundos = 30; break;
  case "60": $segundos = 60; break;
  case "120": $segundos = 120; break;
  case "300": $segundos = 300; break;
  case "1800": $segundos = 1800; break;
  case "3600": $segundos = 3600; break;
  case "7200": $segundos = 7200; break;
  default: $segundos = intval($periodo);
}

// Consulta agrupando en intervalos
$sql = "
SELECT 
  FROM_UNIXTIME(
    FLOOR(UNIX_TIMESTAMP(STR_TO_DATE(CONCAT(fecha,' ',hora), '%Y-%m-%d %H:%i:%s')) / ?) * ?
  ) AS tiempo,
  AVG(temperatura) AS temp_prom,
  AVG(humedad) AS hum_prom,
  AVG(actividad_entrante) AS act_entrante_prom,
  AVG(actividad_saliente) AS act_saliente_prom
FROM datos_medidos
WHERE colmena_id = ?
  AND STR_TO_DATE(CONCAT(fecha,' ',hora), '%Y-%m-%d %H:%i:%s') >= ?
GROUP BY tiempo
ORDER BY tiempo ASC
LIMIT 50;
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("iiis", $segundos, $segundos, $id_colmena, $start);
$stmt->execute();
$res = $stmt->get_result();

$datos = [];
while ($row = $res->fetch_assoc()) {
  $datos[] = [
    "tiempo" => $row["tiempo"],
    "temperatura" => floatval($row["temp_prom"]),
    "humedad" => floatval($row["hum_prom"]),
    "actividad_entrante" => floatval($row["act_entrante_prom"]),
    "actividad_saliente" => floatval($row["act_saliente_prom"])
  ];
}

echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
?>
