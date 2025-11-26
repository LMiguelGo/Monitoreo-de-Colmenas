<?php
header('Content-Type: application/json; charset=utf-8');
require_once("../config/conexion_bd.php");

// Parámetros
$id_colmena = isset($_GET["id_colmena"]) ? intval($_GET["id_colmena"]) : 0;
$inicio     = isset($_GET["inicio"]) ? $_GET["inicio"] : date("Y-m-d H:i:s", strtotime("-1 hour"));
$fin        = isset($_GET["fin"]) ? $_GET["fin"] : date("Y-m-d H:i:s");
$periodo    = isset($_GET["periodo"]) ? $_GET["periodo"] : "60"; // 1 min por defecto

// Conexión
$conexion = new mysqli($host, $user, $pw, $db);
if ($conexion->connect_error) {
  http_response_code(500);
  echo json_encode(["error" => "Error de conexión"]);
  exit;
}

// Convertir el periodo a segundos
$segundos = intval($periodo);

// Consulta agrupando por intervalos
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
  AND STR_TO_DATE(CONCAT(fecha,' ',hora), '%Y-%m-%d %H:%i:%s') BETWEEN ? AND ?
GROUP BY tiempo
ORDER BY tiempo ASC;
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("iiiss", $segundos, $segundos, $id_colmena, $inicio, $fin);
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
